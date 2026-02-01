<?php

namespace app\controllers;

use app\components\Searcher;
use app\models\forms\SearchForm;
use app\models\StaticContent;
use app\models\Teacher;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TeacherController extends Controller
{
    public function actionIndex()
    {
        $form = new SearchForm();
        $form->load(Yii::$app->request->get(), '');
        $form->type = 'teachers';
        $form->per_page = 12;

        $searcher = new Searcher();
        $result = $searcher->search($form);

        $initial_results = $this->renderPartial('/search/_results', [
            'result' => $result,
        ]);

        $staticContent = StaticContent::findByKey('teachers-index');
        return $this->render('index', [
            'initial_results' => $initial_results,
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
