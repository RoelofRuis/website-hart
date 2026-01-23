<?php

namespace app\controllers;

use app\models\Location;
use app\models\StaticContent;
use Yii;
use yii\caching\TagDependency;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class StaticController extends Controller
{
    public function actionCopyright()
    {
        return $this->renderStatic('copyright', 'copyright');
    }

    public function actionAssociation()
    {
        return $this->renderStatic('association', 'association');
    }

    public function actionContact()
    {
        return $this->renderStatic('contact', 'contact');
    }

    public function actionAvg()
    {
        return $this->renderStatic('privacy', 'avg');
    }

    public function actionLocations()
    {
        return $this->renderStatic('locations', 'locations', [
            'locations' => Location::find()->all(),
        ]);
    }

    public function actionAbout()
    {
        return $this->renderStatic('about', 'about');
    }

    public function actionInstrumentRental()
    {
        return $this->renderStatic('instrumentenverhuur', 'instrument-rental');
    }

    public function actionYouthFund()
    {
        return $this->renderStatic('jeugdfonds', 'youth-fund');
    }

    private function renderStatic(string $slug, string $view, array $params = []): Response|string
    {
        $row = Yii::$app->cache->getOrSet([__METHOD__, 'slug' => $slug], function () use ($slug) {
            return StaticContent::find()->where(['slug' => $slug])->asArray()->one() ?: [];
        }, 600, new TagDependency(['tags' => [
            'static-content',
            'static-content:slug:' . $slug,
        ]]));

        if (empty($row)) {
            throw new NotFoundHttpException('Page not found');
        }

        $model = new StaticContent();
        $model->setAttributes($row, false);

        $response = Yii::$app->response;

        // If there are flash messages, do not use HTTP caching
        $session = Yii::$app->session;
        if (!empty($session->getAllFlashes())) {
            return $this->render($view,
                array_merge($params, ['model' => $model])
            );
        }

        // Compute ETag from slug + content hash (stable even without updated_at)
        $etag = 'W/"' . sha1($model->slug . ':' . $model->content) . '"';
        $response->headers->set('ETag', $etag);

        // Last-Modified if available on the model (optional column)
        $lastModified = null;
        if (!empty($model->updated_at)) {
            $ts = is_numeric($model->updated_at) ? (int)$model->updated_at : strtotime((string)$model->updated_at);
            if ($ts) {
                $lastModified = gmdate('D, d M Y H:i:s', $ts) . ' GMT';
                $response->headers->set('Last-Modified', $lastModified);
            }
        }

        $response->headers->set('Cache-Control', 'public, max-age=600, stale-while-revalidate=60');

        $req = Yii::$app->request;
        $ifNoneMatch = $req->headers->get('If-None-Match');
        $ifModifiedSince = $req->headers->get('If-Modified-Since');

        if ($ifNoneMatch && trim($ifNoneMatch) === $etag) {
            $response->statusCode = 304;
            return $response;
        }

        if ($lastModified && $ifModifiedSince && (strtotime($ifModifiedSince) >= strtotime($lastModified))) {
            $response->statusCode = 304;
            return $response;
        }

        return $this->render($view,
            array_merge($params, ['model' => $model])
        );
    }
}
