<?php

namespace app\controllers;

use app\models\ContactMessage;
use app\models\ContactNotification;
use Yii;
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
        /** @var \app\models\Teacher $current */
        $current = Yii::$app->user->identity;
        if (!$current) {
            throw new NotFoundHttpException('Teacher not found.');
        }

        $messagesQuery = ContactMessage::find()
            ->alias('cm')
            ->joinWith(['teachers t', 'lessonFormat lf'])
            ->where(['t.id' => $current->id])
            ->orWhere(['lf.teacher_id' => $current->id])
            ->orderBy(['cm.created_at' => SORT_DESC])
            ->groupBy('cm.id');

        $messages = $messagesQuery->all();
        foreach ($messages as $message) {
            $alreadyOpened = ContactNotification::find()->where([
                'contact_message_id' => $message->id,
                'type' => ContactNotification::TYPE_OPENED
            ])->exists();

            if (!$alreadyOpened) {
                $notification = new ContactNotification();
                $notification->contact_message_id = $message->id;
                $notification->type = ContactNotification::TYPE_OPENED;
                $notification->save();
            }
        }

        return $this->render('messages', [
            'messages' => $messages,
        ]);
    }

}