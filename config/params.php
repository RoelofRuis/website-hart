<?php

return [
    'adminEmail' => getenv('ADMIN_EMAIL') ?: 'no-reply@example.com',
    'storage.driver' => getenv('STORAGE_DRIVER') ?: 'local', // local|s3
    'storage.s3.bucket' => getenv('S3_BUCKET') ?: '',
    'storage.s3.region' => getenv('S3_REGION') ?: '',
    'storage.s3.endpoint' => getenv('S3_ENDPOINT') ?: '',
    'storage.s3.key' => getenv('S3_KEY') ?: '',
    'storage.s3.secret' => getenv('S3_SECRET') ?: '',
];
