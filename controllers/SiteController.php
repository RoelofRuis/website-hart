<?php

namespace app\controllers;

use app\models\ContactMessage;
use app\models\Course;
use app\models\forms\LoginForm;
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

    public function actionSearch()
    {
        return $this->render('search');
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
        $unread_count = ContactMessage::getUnreadCount(Yii::$app->user->id);

        $is_admin = Yii::$app->user->identity->is_admin;
        $is_teacher = Yii::$app->user->identity->getTeacher()->exists();

        return $this->render('manage', [
            'unread_count' => $unread_count,
            'is_admin' => $is_admin,
            'is_teacher' => $is_teacher,
        ]);
    }

    public function actionSitemap()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/xml; charset=UTF-8');

        $urls = [];

        $urls[] = Url::to(['site/index'], true);
        $urls[] = Url::to(['static/contact'], true);
        $urls[] = Url::to(['static/avg'], true);
        $urls[] = Url::to(['static/association'], true);
        $urls[] = Url::to(['static/locations'], true);
        $urls[] = Url::to(['static/copyright'], true);
        $urls[] = Url::to(['static/about'], true);
        $urls[] = Url::to(['static/instrument-rental'], true);
        $urls[] = Url::to(['static/youth-fund'], true);

        $indexed_courses = Course::findIndexable()
            ->select(['slug'])
            ->indexBy('slug');

        foreach ($indexed_courses->column() as $course_slug) {
            $urls[] = Url::to(['course/view', 'slug' => $course_slug], true);
        }

        $indexed_teachers = Teacher::findIndexable()
            ->select(['slug'])
            ->indexBy('slug');

        foreach ($indexed_teachers->column() as $teacher_slug) {
            $urls[] = Url::to(['teacher/view', 'slug' => $teacher_slug], true);
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
