<?php

namespace app\controllers;

use app\models\ContactMessage;
use app\models\ContactMessageUser;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
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
                'only' => ['messages'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['messages'],
                        'roles' => ['@'],
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

        $messagesQuery = ContactMessage::find()
            ->alias('cm')
            ->innerJoin('{{%contact_message_user}} cmu', 'cmu.contact_message_id = cm.id')
            ->where(['cmu.user_id' => $current->id])
            ->orderBy(['cm.created_at' => SORT_DESC]);

        $messages = $messagesQuery->all();
        foreach ($messages as $message) {
            $notification = ContactMessageUser::find()->where([
                'contact_message_id' => $message->id,
                'user_id' => $current->id,
            ])->one();

            if (!$notification instanceof ContactMessageUser) {
                continue;
            }

            if (!empty($notification->notified_at)) {
                continue;
            }

            $notification->notified_at = new Expression('NOW()');
            $notification->save();
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $messagesQuery,
        ]);

        return $this->render('messages', [
            'dataProvider' => $dataProvider,
        ]);
    }

}