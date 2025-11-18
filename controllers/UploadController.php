<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\imagine\Image;

class UploadController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['image'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionImage(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $file = UploadedFile::getInstanceByName('file');
        if (!$file) {
            throw new BadRequestHttpException('No file uploaded');
        }

        // Basic validation
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file->type, $allowed, true)) {
            throw new BadRequestHttpException('Unsupported image type. Allowed: JPG, PNG, WEBP');
        }
        if ($file->error !== UPLOAD_ERR_OK) {
            throw new BadRequestHttpException('Upload error: ' . $file->error);
        }

        // Load and resize to 400x400, preserving aspect ratio then fit
        $tmpPath = $file->tempName;
        try {
            $im = Image::getImagine()->open($tmpPath);
            $thumb = Image::thumbnail($im, 400, 400);
            // Encode to JPEG to normalize
            $contents = $thumb->get('jpg', ['jpeg_quality' => 85]);
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw new BadRequestHttpException('Invalid image content');
        }

        $uuid = bin2hex(random_bytes(8));
        $path = sprintf('teachers/%s/%s/%s.jpg', date('Y'), date('m'), $uuid);

        $url = Yii::$app->storage->put($path, $contents, [
            'visibility' => 'public',
            'ContentType' => 'image/jpeg',
        ]);

        return [
            'success' => true,
            'url' => $url,
            'path' => $path,
        ];
    }
}
