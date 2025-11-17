<?php

namespace app\controllers;

use app\models\Course;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CourseController extends Controller
{
    public function actionIndex()
    {
        $courses = Course::find()->all();
        return $this->render('index', [
            'courses' => $courses,
        ]);
    }

    public function actionView($slug = null)
    {
        $model = Course::findBySlug($slug);
        if (!$model) {
            throw new NotFoundHttpException('Course not found.');
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }
}
