<?php

/**
 * @var yii\web\View $this
 * @var string $name
 * @var string|null $value
 * @var string $inputId
 * @var string $previewId
 * @var string $hiddenId
 * @var string $helpId
 * @var string $uploadUrl
 * @var int $previewSize
 * @var string $csrfParam
 * @var string $csrfToken
 */

use yii\helpers\Html;

?>

<?= Html::hiddenInput($name, $value, ['id' => $hiddenId]); ?>
<div class="mb-2">
    <?php if ($value): ?>
        <?= Html::img($value, [
                'id' => $previewId,
                'class' => 'img-thumbnail rounded',
                'style' => "max-width: {$previewSize}px; max-height: {$previewSize}px;",
        ]) ?>
    <?php else: ?>
        <?= Html::tag('div', '', [
                'id' => $previewId,
                'class' => 'img-thumbnail rounded',
                'style' => "width: {$previewSize}px; height: {$previewSize}px; background: #f0f0f0; display: inline-block;",
        ]) ?>
    <?php endif; ?>
</div>
<?= Html::fileInput('file', null, [
        'id' => $inputId,
        'accept' => 'image/*',
        'class' => 'form-control',
        'data-image-upload' => '1',
        'data-upload-url' => $uploadUrl,
        'data-target-hidden' => $hiddenId,
        'data-target-preview' => $previewId,
        'data-help-id' => $helpId,
        'data-csrf-param' => $csrfParam,
        'data-csrf-token' => $csrfToken,
]) ?>

<div class="form-text" id="<?= $helpId ?>"></div>
