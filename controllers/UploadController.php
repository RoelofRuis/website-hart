<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\imagine\Image;
use Imagine\Image\Point;
use Imagine\Image\Palette\RGB;

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
        // Validate using actual file content, not just client-provided type
        $imgInfo = @getimagesize($file->tempName);
        $realMime = $imgInfo['mime'] ?? null;
        if (!$realMime || !in_array($realMime, $allowed, true)) {
            throw new BadRequestHttpException('Unsupported or invalid image type. Allowed: JPG, PNG, WEBP');
        }
        if ($file->error !== UPLOAD_ERR_OK) {
            throw new BadRequestHttpException('Upload error: ' . $file->error);
        }

        // Load and resize to 400x400, preserving aspect ratio then fit
        $tmpPath = $file->tempName;
        try {
            $im = Image::getImagine()->open($tmpPath);
            $thumb = Image::thumbnail($im, 400, 400);

            // If the image has transparency (e.g., PNG/WebP with alpha),
            // paste it onto a white canvas before encoding to JPEG
            $size = $thumb->getSize();
            $palette = new RGB();
            $white = $palette->color('#ffffff');
            $canvas = Image::getImagine()->create($size, $white);
            $canvas->paste($thumb, new Point(0, 0));

            // Encode to JPEG to normalize
            $contents = $canvas->get('jpg', ['jpeg_quality' => 85]);
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw new BadRequestHttpException('Invalid image content');
        }

        // Persist using storage service which handles id generation and metadata
        $result = Yii::$app->storage->save($contents, 'image/jpeg');

        return [
            'success' => true,
            'url' => $result['url'],
            'id' => $result['id'],
        ];
    }
}
