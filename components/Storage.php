<?php

namespace app\components;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Visibility;
use Aws\S3\S3Client;
use Yii;

class Storage
{
    private Filesystem $fs;
    private string $baseUrl;

    public function __construct()
    {
        $params = Yii::$app->params;
        $driver = $params['storage.driver'] ?? 'local';

        if ($driver === 's3') {
            $bucket = $params['storage.s3.bucket'] ?? '';
            $region = $params['storage.s3.region'] ?? '';
            $endpoint = $params['storage.s3.endpoint'] ?? null;
            $key = $params['storage.s3.key'] ?? null;
            $secret = $params['storage.s3.secret'] ?? null;
            $clientConfig = [
                'version' => 'latest',
                'region' => $region,
            ];
            if ($endpoint) {
                $clientConfig['endpoint'] = $endpoint;
            }
            if ($key && $secret) {
                $clientConfig['credentials'] = ['key' => $key, 'secret' => $secret];
            }
            $client = new S3Client($clientConfig);
            $adapter = new AwsS3V3Adapter($client, $bucket);
            $this->baseUrl = rtrim($params['storage.baseUrl'] ?? '', '/');
        } else {
            $path = Yii::getAlias('@webroot/uploads');
            if (!is_dir($path)) {
                @mkdir($path, 0775, true);
            }
            $adapter = new LocalFilesystemAdapter($path);
            $this->baseUrl = rtrim($params['storage.baseUrl'] ?? '/uploads', '/');
        }

        $this->fs = new Filesystem($adapter);
    }

    public function put(string $path, string $contents, array $config = []): string
    {
        $path = ltrim($path, '/');
        // Ensure uploaded files are world-readable by the web server (public visibility)
        if (!isset($config['visibility'])) {
            $config['visibility'] = Visibility::PUBLIC;
        }
        $this->fs->write($path, $contents, $config);
        return $this->url($path);
    }

    public function delete(string $path): void
    {
        $path = ltrim($path, '/');
        if ($this->fs->fileExists($path)) {
            $this->fs->delete($path);
        }
    }

    public function url(string $path): string
    {
        $path = ltrim($path, '/');
        if ($this->baseUrl) {
            return $this->baseUrl . '/' . $path;
        }
        return '/' . $path;
    }
}
