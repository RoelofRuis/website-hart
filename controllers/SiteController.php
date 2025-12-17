<?php

namespace app\controllers;

use app\models\CourseNode;
use app\models\forms\LoginForm;
use app\models\forms\ContactForm;
use app\models\Location;
use app\models\StaticContent;
use app\models\Teacher;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
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
            'homeTitle' => StaticContent::findByKey('home-title'),
            'homeNews' => StaticContent::findByKey('home-news'),
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
            return $this->redirect(['site/manage']);
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
        return $this->render('manage');
    }

    public function actionSitemap()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/xml; charset=UTF-8');

        $urls = [];

        $urls[] = Url::to(['site/index'], true);
        $urls[] = Url::to(['site/contact'], true);
        $urls[] = Url::to(['site/avg'], true);
        $urls[] = Url::to(['site/association'], true);
        $urls[] = Url::to(['site/locations'], true);
        $urls[] = Url::to(['site/copyright'], true);

        foreach (CourseNode::find()->all() as $course) {
            $urls[] = Url::to(['course/view', 'slug' => $course->slug], true);
        }

        foreach (Teacher::find()->all() as $teacher) {
            $urls[] = Url::to(['teacher/view', 'slug' => $teacher->slug], true);
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $loc) {
            $xml .= '<url><loc>' . htmlspecialchars($loc, ENT_XML1 | ENT_COMPAT, 'UTF-8') . '</loc></url>';
        }
        $xml .= "</urlset>";

        Yii::$app->response->content = $xml;
        return Yii::$app->response;
    }
}
