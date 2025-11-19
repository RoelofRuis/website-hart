<?php

namespace app\components;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Aws\S3\S3Client;
use Yii;
use app\models\File;

class Storage
{
    private Filesystem $fs;

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
        } else {
            $path = Yii::getAlias('@app/storage');
            if (!is_dir($path)) {
                @mkdir($path, 0775, true);
            }
            $adapter = new LocalFilesystemAdapter($path);
        }

        $this->fs = new Filesystem($adapter);
    }

    public function put(string $path, string $contents, array $config = []): string
    {
        $path = ltrim($path, '/');
        // Do not enforce public visibility; files are served via controller endpoint
        $this->fs->write($path, $contents, $config);
        return $path;
    }

    public function delete(string $path): void
    {
        $path = ltrim($path, '/');
        if ($this->fs->fileExists($path)) {
            $this->fs->delete($path);
        }
    }

    public function read(string $path): string
    {
        $path = ltrim($path, '/');
        return $this->fs->read($path);
    }

    public function fileExists(string $path): bool
    {
        $path = ltrim($path, '/');
        return $this->fs->fileExists($path);
    }

    /**
     * Persist a blob to storage and its metadata to the DB, returning slug/id and serving url.
     * Options:
     * - slug: provide a deterministic slug; if omitted, a random opaque slug is generated
     */
    public function save(string $contents, string $contentType, array $options = []): array
    {
        $slug = isset($options['slug']) && is_string($options['slug']) && $options['slug'] !== ''
            ? $options['slug']
            : bin2hex(random_bytes(8));

        $ext = $this->extensionFromContentType($contentType) ?? 'bin';
        $path = sprintf('%s_%s_%s.%s', date('Y'), date('m'), bin2hex(random_bytes(6)), $ext);

        $writtenPath = $this->put($path, $contents, [
            'ContentType' => $contentType,
        ]);

        // Upsert metadata by slug
        $file = File::findBySlug($slug) ?? new File();
        $file->slug = $slug;
        $file->storage_path = $writtenPath;
        $file->content_type = $contentType;
        $file->size = strlen($contents);
        $file->save(false);

        return [
            'id' => $file->slug,
            'url' => '/files/' . $file->slug,
            'storage_path' => $writtenPath,
            'size' => $file->size,
            'content_type' => $file->content_type,
        ];
    }

    private function extensionFromContentType(?string $contentType): ?string
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
        ];
        return $contentType && isset($map[$contentType]) ? $map[$contentType] : null;
    }
}
