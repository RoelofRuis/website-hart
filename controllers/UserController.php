<?php

namespace app\controllers;

use app\models\Teacher;
use app\models\User;
use Yii;
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
                        'actions' => ['update', 'admin', 'create', 'delete'],
                        'roles' => ['@'],
                    ]
                ]
            ]
        ];
    }

    public function actionAdmin()
    {
        $dataProvider = new \yii\data\ActiveDataProvider([
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
        $user->is_active = true;
        $teacher = null;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if ($user->load($post)) {
                // Set a random password for new users
                $user->setPassword(Yii::$app->security->generateRandomString(12));
                $user->generateAuthKey();

                if (!empty($post['make_teacher'])) {
                    $teacher = new Teacher();
                    $teacher->load($post);
                    // user_id will be set after user save
                }

                $valid = $user->validate();
                if ($teacher) {
                    // We need to bypass the user_id required validation for now or set it to a dummy
                    $teacher->user_id = 0; 
                    $valid = $teacher->validate() && $valid;
                }

                if ($valid) {
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
                    $user->is_admin = (bool) $user->getOldAttribute('is_admin');
                    $user->is_active = (bool) $user->getOldAttribute('is_active');
                }
                
                $valid = $user->validate();
                if ($teacher) {
                    $valid = $teacher->validate() && $valid;
                }

                if ($valid) {
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


}