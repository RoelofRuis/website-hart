<?php

namespace app\controllers;

use app\models\Teacher;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;

class TeacherController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['update'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $teachers = Teacher::find()->all();
        return $this->render('index', [
            'teachers' => $teachers,
        ]);
    }

    public function actionView(string $slug)
    {
        $model = Teacher::findBySlug($slug);
        if (!$model) {
            throw new NotFoundHttpException('Teacher not found.');
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = Teacher::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Teacher not found.');
        }

        $current = Yii::$app->user->identity;
        if (!$current) {
            throw new NotFoundHttpException('Not authorized.');
        }

        $canEdit = $current->admin || ($current->id === $model->id);
        if (!$canEdit) {
            throw new NotFoundHttpException('You are not allowed to edit this teacher.');
        }

        // Restrict editable attributes for security
        $safeAttributes = ['full_name', 'email', 'telephone', 'profile_picture', 'description', 'course_type_id'];
        if ($current->admin) {
            $safeAttributes[] = 'admin';
        }

        if ($model->load(Yii::$app->request->post())) {
            // Prevent privilege escalation by non-admins
            if (!$current->admin) {
                $model->admin = (bool)$model->getOldAttribute('admin');
            }
            if ($model->save()) {
            Yii::$app->session->setFlash('success', 'Teacher information updated successfully.');
            return $this->redirect(['view', 'slug' => $model->slug]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'safeAttributes' => $safeAttributes,
        ]);
    }
}
