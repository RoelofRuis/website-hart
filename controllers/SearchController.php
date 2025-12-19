<?php

namespace app\controllers;

use app\components\Searcher;
use app\models\forms\SearchForm;
use Yii;
use yii\web\Controller;

class SearchController extends Controller
{
    public function actionIndex(string $q = null)
    {
        $form = new SearchForm();
        $form->load(Yii::$app->request->get(), '');

        $searcher = new Searcher();

        list($results, $has_more) = $searcher->search($form);

        return $this->renderPartial('_results', [
            'q' => $q,
            'results' => $results,
            'hasMore' => $has_more,
            'nextPage' => $has_more ? $form->page + 1 : null,
            'suppressEmpty' => $form->suppress_empty,
        ]);
    }
}
