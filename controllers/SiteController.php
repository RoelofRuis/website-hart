<?php

namespace app\controllers;

use app\models\forms\LoginForm;
use app\models\forms\ContactForm;
use app\models\Location;
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
                'only' => ['logout', 'manage'],
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
        return $this->render('index', [
            'homeContent' => StaticContent::findByKey('home'),
        ]);
    }

    public function actionCopyright()
    {
        return $this->render('copyright', [
            'content' => StaticContent::findByKey('copyright'),
        ]);
    }

    public function actionAssociation()
    {
        return $this->render('association', [
            'content' => StaticContent::findByKey('association'),
        ]);
    }

    public function actionContact()
    {
        return $this->render('contact', [
            'content' => StaticContent::findByKey('contact'),
        ]);
    }

    public function actionAvg()
    {
        return $this->render('avg', [
            'content' => StaticContent::findByKey('privacy'),
        ]);
    }

    public function actionLocations()
    {
        return $this->render('locations', [
            'locations' => Location::find()->all(),
            'content' => StaticContent::findByKey('locations'),
        ]);
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

    public function actionManage()
    {
        // Only reachable for authenticated users due to AccessControl
        return $this->render('manage');
    }
}
