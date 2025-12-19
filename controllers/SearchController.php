<?php

namespace app\controllers;

use app\components\Searcher;
use app\models\forms\SearchForm;
use Yii;
use yii\web\Controller;

class SearchController extends Controller
{
    public function actionIndex()
    {
        $form = new SearchForm();
        $form->load(Yii::$app->request->get(), '');

        $searcher = new Searcher();

        $result = $searcher->search($form);

        return $this->renderPartial('_results', [
            'result' => $result,
        ]);
    }
}
