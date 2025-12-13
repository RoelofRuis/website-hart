<?php

namespace app\controllers;

use app\models\forms\LoginForm;
use app\models\forms\ContactForm;
use app\models\StaticContent;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCopyright()
    {
        $content = StaticContent::findByKey('copyright');

        return $this->render('copyright', [
            'content' => $content,
        ]);
    }

    public function actionAssociation()
    {
        $content = StaticContent::findByKey('association');

        return $this->render('association', [
            'content' => $content,
        ]);
    }

    public function actionContact()
    {
        $content = StaticContent::findByKey('contact');

        return $this->render('contact', [
            'content' => $content,
        ]);
    }

    public function actionAvg()
    {
        $content = StaticContent::findByKey('privacy');

        return $this->render('avg', [
            'content' => $content,
        ]);
    }

    public function actionLocations()
    {
        return $this->render('locations');
    }

    public function actionContactSubmit(): Response
    {
        $model = new ContactForm();
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
        return $this->redirect($referrer ?: ['site/contact']);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
