<?php

namespace app\controllers;

use app\components\Storage;
use app\models\File;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class FileController extends Controller
{
    private Storage $storage;

    public $enableCsrfValidation = false;

    public function __construct($id, $module, Storage $storage, $config = [])
    {
        parent::__construct($id, $module, $config);

        $this->storage = $storage;
    }

    public function actionView(string $slug)
    {
        /** @var File|null $file */
        $file = File::findBySlug($slug);
        if (!$file) {
            throw new NotFoundHttpException('File not found');
        }

        if (!$this->storage->fileExists($file->storage_path)) {
            throw new NotFoundHttpException('File not found');
        }

        $contents = $this->storage->read($file->storage_path);

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
