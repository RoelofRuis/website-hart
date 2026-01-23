<?php

namespace app\controllers;

use app\models\Teacher;
use app\models\StaticContent;
use Yii;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TeacherController extends Controller
{
    public function actionIndex()
    {
        $q = Yii::$app->request->get('q');

        $query = Teacher::findIndexable();
        if ($q !== null && $q !== '') {
            $query->andFilterWhere(['or',
                ['ILIKE', 'user.full_name', $q],
                ['ILIKE', 'description', $q],
            ]);
            // Prefer name matches over description matches
            $query->orderBy(new Expression(
                "CASE WHEN user.full_name ILIKE :qprefix THEN 0 WHEN user.full_name ILIKE :qany THEN 1 ELSE 2 END, user.full_name ASC",
            ))
                ->addParams([
                    ':qprefix' => $q . '%',
                    ':qany' => '%' . $q . '%',
                ]);
        } else {
            $query->orderBy(['user.full_name' => SORT_ASC]);
        }
        $teachers = $query->all();
        $staticContent = StaticContent::findByKey('teachers-index');
        return $this->render('index', [
            'teachers' => $teachers,
            'q' => $q,
            'staticContent' => $staticContent,
        ]);
    }

    public function actionView(string $slug)
    {
        $teacher = Teacher::findBySlug($slug)->with('user')->one();
        if (!$teacher instanceof Teacher) {
            throw new NotFoundHttpException('Teacher not found.');
        }

        $courses = $teacher->getAccessibleCourses()->orderBy(['name' => SORT_ASC])->all();

        return $this->render('view', [
            'teacher' => $teacher,
            'courses' => $courses,
        ]);
    }
}
