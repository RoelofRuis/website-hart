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