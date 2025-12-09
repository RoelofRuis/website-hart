<?php

namespace app\controllers;

use app\models\Course;
use Yii;
use yii\web\Controller;

class SearchController extends Controller
{
    /**
     * Endpoint stub that returns server-rendered HTML blocks for the search query.
     * Accepts GET param `q` and renders a partial view without layout.
     */
    public function actionIndex(string $q = null)
    {
        $q = $q ?? Yii::$app->request->get('q');

        $courses = [];
        if ($q !== null && trim($q) !== '' && mb_strlen($q) >= 2) {
            $courses = Course::find()
                ->andWhere(['or',
                    ['ILIKE', 'name', $q],
                    ['ILIKE', 'summary', $q],
                    ['ILIKE', 'description', $q],
                ])
                ->orderBy(['name' => SORT_ASC])
                ->limit(10)
                ->all();
        }

        return $this->renderPartial('results', [
            'q' => $q,
            'courses' => $courses,
        ]);
    }
}
