<?php

namespace app\controllers;

use app\models\StaticContent;
use Yii;
use yii\caching\TagDependency;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class StaticContentController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['admin', 'update'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->is_admin;
                        },
                    ],
                ],
            ],
        ];
    }

    public function actionAdmin()
    {
        $query = StaticContent::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
            'sort' => [
                'defaultOrder' => ['key' => SORT_ASC],
            ],
        ]);

        return $this->render('admin', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdate(string $key)
    {
        $model = StaticContent::findOne($key);
        if (!$model) {
            throw new NotFoundHttpException('Static content not found.');
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                TagDependency::invalidate(Yii::$app->cache, [
                    'static-content',
                    'static-content:key:' . $model->key,
                    'static-content:slug:' . $model->slug,
                ]);
                Yii::$app->session->setFlash('success', Yii::t('app', 'Saved'));
                return $this->redirect(['static-content/admin']);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Please correct the errors in the form.'));
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }
}
