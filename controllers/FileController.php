<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\File;

class FileController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionView(string $slug)
    {
        /** @var File|null $file */
        $file = File::findBySlug($slug);
        if (!$file) {
            throw new NotFoundHttpException('File not found');
        }

        $storage = Yii::$app->storage;
        if (!$storage->fileExists($file->storage_path)) {
            throw new NotFoundHttpException('File not found');
        }

        $contents = $storage->read($file->storage_path);

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        if (!empty($file->content_type)) {
            $response->headers->set('Content-Type', $file->content_type);
        }
        if ($file->size) {
            $response->headers->set('Content-Length', (string)$file->size);
        }
        $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        $response->content = $contents;
        return $response;
    }
}
