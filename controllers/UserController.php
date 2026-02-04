<?php

namespace app\controllers;

use app\models\Teacher;
use app\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'admin', 'create', 'delete', 'resend-activation', 'request-password-reset'],
                        'roles' => ['@'],
                    ]
                ]
            ]
        ];
    }

    public function actionAdmin()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find()->orderBy(['full_name' => SORT_ASC]),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('admin', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        if (!Yii::$app->user->identity->is_admin) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        $user = new User();
        $user->scenario = 'create';
        $user->is_active = false;
        $teacher = null;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($user->load($post)) {
                $user->generateAuthKey();
                $user->generateActivationToken();
                // Admin cannot set password directly for others
                $user->password_hash = Yii::$app->security->generateRandomString(); 

                if (!empty($post['make_teacher'])) {
                    $teacher = new Teacher();
                    $teacher->load($post);
                    // user_id will be set after user save
                }

                if ($user->save()) {
                    if ($teacher) {
                        $teacher->user_id = $user->id;
                        if (empty($teacher->slug)) {
                            $teacher->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $user->full_name), '-'));
                        }
                        $teacher->save(false);
                    }
                    Yii::$app->session->setFlash('success', Yii::t('app', 'User created successfully.'));
                    return $this->redirect(['user/admin']);
                }
            }
        }

        return $this->render('create', [
            'user' => $user,
            'teacher' => $teacher ?? new Teacher(),
        ]);
    }

    public function actionDelete(int $id)
    {
        if (!Yii::$app->user->identity->is_admin) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        if ($user->id === Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', Yii::t('app', 'You cannot delete yourself.'));
            return $this->redirect(['admin']);
        }

        // Delete associated teacher record if it exists
        $teacher = $user->getTeacher()->one();
        if ($teacher) {
            $teacher->delete();
        }

        $user->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', 'User deleted.'));
        return $this->redirect(['admin']);
    }

    public function actionUpdate(int $id)
    {
        $current = Yii::$app->user->identity;
        if (!$current) {
            throw new NotFoundHttpException('Not allowed.');
        }

        /** @var User $user */
        $user = User::findOne($id);
        if (!$user instanceof User) {
            throw new NotFoundHttpException('User not found.');
        }

        $can_edit = $current->is_admin || ($current->getId() === $user->id);
        if (!$can_edit) {
            throw new NotFoundHttpException('User not found.');
        }

        /** @var Teacher $teacher */
        $teacher = $user->getTeacher()->one();
        if (class_exists('Yii')) {
            file_put_contents(\Yii::getAlias('@runtime/logs/debug.log'), "UserController: teacher for user {$id} is " . ($teacher ? "found (ID {$teacher->id})" : "NOT found") . "\n", FILE_APPEND);
        }

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            $user_loaded = $user->load($post);

            $teacher_loaded = false;
            if ($teacher) {
                $teacher_loaded = $teacher->load($post);
            } elseif ($current->is_admin && !empty($post['make_teacher'])) {
                $teacher = new Teacher();
                $teacher->user_id = $user->id;
                $teacher->slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $user->full_name), '-'));
                $teacher_loaded = $teacher->load($post);
            }

            if ($user_loaded) {
                if (!$current->is_admin) {
                    // Non-admins cannot change these
                    $user->is_admin = (bool)$user->getOldAttribute('is_admin');
                    $user->is_active = (bool)$user->getOldAttribute('is_active');
                }

                $valid = $user->validate();
                if ($teacher) {
                    $valid = $teacher->validate() && $valid;
                }

                if ($valid) {
                    if (!empty($user->password)) {
                        // Only allow user to set their OWN password
                        if ($current->id === $user->id) {
                            $user->setPassword($user->password);
                        }
                    }
                    $user->save(false);
                    if ($teacher) {
                        $teacher->save(false);
                    }
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Profile updated successfully.'));
                    if ($current->id === $user->id) {
                        return $this->redirect(['site/manage']);
                    }
                    return $this->redirect(['user/admin']);
                }
            }
        }

        return $this->render('update', [
            'user' => $user,
            'teacher' => $teacher,
        ]);
    }

    public function actionResendActivation(int $id)
    {
        if (!Yii::$app->user->identity->is_admin) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        if ($user->is_active) {
            Yii::$app->session->setFlash('info', Yii::t('app', 'User is already active.'));
            return $this->redirect(['admin']);
        }

        $user->generateActivationToken();
        if ($user->save(false) && $user->sendActivationEmail()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Activation link sent.'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Failed to send activation link.'));
        }

        return $this->redirect(['admin']);
    }

    public function actionRequestPasswordReset(int $id)
    {
        if (!Yii::$app->user->identity->is_admin) {
            throw new ForbiddenHttpException('Not allowed.');
        }

        $user = User::findOne($id);
        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        $user->generatePasswordResetToken();
        if ($user->save(false) && $user->sendPasswordResetEmail()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Check your email for further instructions.'));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('app', 'Sorry, we are unable to reset password for the provided email address.'));
        }

        return $this->redirect(['admin']);
    }
}