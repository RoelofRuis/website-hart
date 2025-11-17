<?php

namespace app\controllers;

use app\models\Teacher;
use Yii;
use yii\db\Expression;
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
        $q = Yii::$app->request->get('q');

        $query = Teacher::find();
        if ($q !== null && $q !== '') {
            $query->andFilterWhere(['or',
                ['ILIKE', 'full_name', $q],
                ['ILIKE', 'description', $q],
            ]);
            // Prefer name matches over description matches
            $query->orderBy(new Expression(
                "CASE WHEN full_name ILIKE :qprefix THEN 0 WHEN full_name ILIKE :qany THEN 1 ELSE 2 END, full_name ASC",
            ))
                ->addParams([
                    ':qprefix' => $q . '%',
                    ':qany' => '%' . $q . '%',
                ]);
        } else {
            $query->orderBy(['full_name' => SORT_ASC]);
        }

        $teachers = $query->all();
        return $this->render('index', [
            'teachers' => $teachers,
            'q' => $q,
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
            throw new NotFoundHttpException('Teacher not found.');
        }

        $canEdit = $current->admin || ($current->id === $model->id);
        if (!$canEdit) {
            throw new NotFoundHttpException('Teacher not found.');
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
