<?php

namespace app\controllers;

use app\models\Course;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CourseController extends Controller
{
    public function actionIndex()
    {
        $q = \Yii::$app->request->get('q');

        $query = Course::find();
        if ($q !== null && $q !== '') {
            $query->andFilterWhere(['or',
                ['ILIKE', 'name', $q],
                ['ILIKE', 'description', $q],
            ]);
            // Prefer name matches over description matches (and prefix matches first)
            $query->orderBy(new Expression(
                "CASE WHEN name ILIKE :qprefix THEN 0 WHEN name LIKE :qany THEN 1 ELSE 2 END, name ASC"
            ))
            ->addParams([
                ':qprefix' => $q . '%',
                ':qany' => '%' . $q . '%',
            ]);
        } else {
            $query->orderBy(['name' => SORT_ASC]);
        }

        $courses = $query->all();
        return $this->render('index', [
            'courses' => $courses,
            'q' => $q,
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
