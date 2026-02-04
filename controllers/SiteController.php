<?php

namespace app\controllers;

use app\components\Searcher;
use app\models\ContactMessageUser;
use app\models\Course;
use app\models\forms\LoginForm;
use app\models\forms\PasswordResetRequestForm;
use app\models\forms\ResetPasswordForm;
use app\models\forms\SearchForm;
use app\models\StaticContent;
use app\models\Teacher;
use app\models\UrlRule;
use app\models\User;
use InvalidArgumentException;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\NotFoundHttpException;
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

    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ($exception instanceof NotFoundHttpException) {
            $url = Yii::$app->request->url;
            $pathInfo = Yii::$app->request->pathInfo;

            // Try matching both full URL and path info
            $rule = UrlRule::find()
                ->where(['source_url' => [$url, '/' . $pathInfo, $pathInfo]])
                ->one();

            if ($rule) {
                $rule->updateCounters(['hit_counter' => 1]);
                return $this->redirect($rule->target_url, 301);
            }
        }

        $errorAction = new ErrorAction('error', $this);
        return $errorAction->run();
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
        $form = new SearchForm();
        $form->load(Yii::$app->request->get(), '');

        $searcher = new Searcher();
        $result = $searcher->search($form);

        $initial_results = $this->renderPartial('/search/_results', [
            'result' => $result,
        ]);

        return $this->render('search', [
            'initial_results' => $initial_results,
        ]);
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

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Check your email for further instructions.'));
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry, we are unable to reset password for the provided email address.'));
        }

        return $this->render('request_password_reset_token', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'New password saved.'));
            return $this->redirect(['site/login']);
        }

        return $this->render('reset_password', [
            'model' => $model,
        ]);
    }

    public function actionActivate(string $token)
    {
        $user = User::findOne(['activation_token' => $token]);
        if (!$user || !$user->isActivationTokenValid($token)) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'The activation link is invalid or has expired. Please contact an administrator.'));
            return $this->goHome();
        }

        if ($user->activate()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Your account has been activated! You can now log in.'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry, we could not activate your account. Please contact an administrator.'));
        }

        return $this->redirect(['site/login']);
    }

    public function actionManage()
    {
        $unread_count = ContactMessageUser::find()
            ->where(['user_id' => Yii::$app->user->id])
            ->andWhere(['notified_at' => null])
            ->count();

        $is_admin = Yii::$app->user->identity->is_admin;
        $is_teacher = Yii::$app->user->identity->getTeacher()->exists();

        $incomplete_static_content = false;
        if ($is_admin) {
            $incomplete_static_content = StaticContent::find()->where(['or', ['content' => ''], ['content' => null]])->exists();
        }

        return $this->render('manage', [
            'unread_count' => $unread_count,
            'is_admin' => $is_admin,
            'is_teacher' => $is_teacher,
            'incomplete_static_content' => $incomplete_static_content,
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
