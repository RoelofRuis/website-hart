<?php

namespace app\controllers;

use app\models\Teacher;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TeacherController extends Controller
{
    public function actionIndex()
    {
        $teachers = Teacher::findAll();
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
}
