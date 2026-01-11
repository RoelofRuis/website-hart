<?php

namespace app\controllers;

use app\models\ContactMessage;
use app\models\ContactMessageSearch;
use app\models\ContactMessageUser;
use app\models\User;
use Yii;
use yii\db\Expression;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ContactController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['messages', 'all-messages', 'update-receivers'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['messages'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['all-messages', 'update-receivers'],
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return Yii::$app->user->identity->isAdmin();
                        }
                    ],
                ],
            ],
        ];
    }

    public function actionSubmit()
    {
        $model = new ContactMessage();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Thank you! Your message has been sent.'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry, we could not send your message. Please try again later.'));
            }
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Please correct the errors in the form.'));
        }

        $referrer = Yii::$app->request->referrer;
        return $this->redirect($referrer ?: ['site/index']);
    }

    public function actionMessages()
    {
        /** @var User $current */
        $current = Yii::$app->user->identity;
        if (!$current) {
            throw new NotFoundHttpException('Teacher not found.');
        }

        $searchModel = new ContactMessageSearch();
        $query = ContactMessage::find()
            ->alias('cm')
            ->innerJoin('{{%contact_message_user}} cmu', 'cmu.contact_message_id = cm.id')
            ->where(['cmu.user_id' => $current->id]);

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $query);

        // Mark as notified
        $messages = $query->all();
        foreach ($messages as $message) {
            $notification = ContactMessageUser::find()->where([
                'contact_message_id' => $message->id,
                'user_id' => $current->id,
            ])->one();

            if (!$notification instanceof ContactMessageUser) {
                continue;
            }

            if (isset($notification->notified_at) && !empty($notification->notified_at)) {
                continue;
            }

            try {
                $notification->notified_at = new Expression('NOW()');
                $notification->save();
            } catch (\Exception $e) {
                // Ignore if column doesn't exist
            }
        }

        return $this->render('messages', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionAllMessages()
    {
        $searchModel = new ContactMessageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('all-messages', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'users' => User::find()->all(),
        ]);
    }

    public function actionUpdateReceivers($id)
    {
        $model = $this->findModel($id);
        $receivers = Yii::$app->request->post('receivers', []);

        ContactMessageUser::deleteAll(['contact_message_id' => $id]);
        foreach ($receivers as $userId) {
            $cmu = new ContactMessageUser();
            $cmu->contact_message_id = $id;
            $cmu->user_id = $userId;
            if (!$cmu->save()) {
                Yii::error('Failed to save ContactMessageUser: ' . print_r($cmu->errors, true));
            }
        }

        return $this->redirect(['all-messages']);
    }

    protected function findModel($id)
    {
        if (($model = ContactMessage::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}