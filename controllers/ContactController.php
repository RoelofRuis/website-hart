<?php

namespace app\controllers;

use app\models\ContactMessage;
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
        $current = Yii::$app->user->identity;
        if (!$current) {
            throw new NotFoundHttpException('Teacher not found.');
        }

        // TODO: Filter!
        $contacts = ContactMessage::find()
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        // Normalize to a common structure
        $items = [];
        foreach ($contacts as $c) {
            /** @var ContactMessage $c */
            $items[] = [
                'type' => 'contact',
                'course' => null,
                'from_name' => $c->name,
                'email' => $c->email,
                'telephone' => null,
                'age' => null,
                'message' => $c->message,
                'created_at' => $c->created_at,
            ];
        }

        // Sort by created_at desc
        usort($items, function ($a, $b) {
            return ($b['created_at'] <=> $a['created_at']);
        });

        return $this->render('messages', [
            'items' => $items,
        ]);
    }

}