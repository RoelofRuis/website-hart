<?php

/**
 * @var string $href
 * @var string $image
 * @var string $title
 * @var string $content
 * @var string $cta
 * @var string $icon
 * @var string $type
 * @var string $tooltip
 * @var boolean $hide_on_mobile
 */

$hide_on_mobile = $hide_on_mobile ?? false;

use yii\helpers\Html;

?>
<a
    href="<?= Html::encode($href) ?>"
    class="text-decoration-none text-reset"
>
    <div class="card h-100 lift-card">
        <div class="position-relative">
            <?php if (!empty($image)): ?>
                <?= Html::img($image, [
                    'class' => $hide_on_mobile ? 'card-img-top d-none d-md-block' : 'card-img-top',
                    'alt' => Html::encode($title),
                    'style' => 'aspect-ratio: 16/9; object-fit: cover;',
                    'loading' => 'lazy',
                ]) ?>
                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-3" style="background: linear-gradient(180deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0) 50%);">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title text-white mb-0 text-shadow"><?= Html::encode($title); ?></h5>
                        <?php if (!empty($icon)): ?>
                            <i class="bi <?= Html::encode($icon) ?> text-white fs-4 text-shadow" title="<?= Html::encode($tooltip) ?>"></i>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="card-header bg-light border-0 py-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <h5 class="card-title mb-0"><?= Html::encode($title); ?></h5>
                        <?php if (!empty($icon)): ?>
                            <i class="bi <?= Html::encode($icon) ?> text-muted fs-4" title="<?= Html::encode($tooltip) ?>"></i>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <p class="card-text text-muted">
                <?= Html::encode(strip_tags($content)); ?>
            </p>
        </div>
        <div class="card-footer p-0 border-0">
            <span class="btn btn-petrol w-100 rounded-0 rounded-bottom py-2 fw-bold" aria-hidden="true">
                <?= Html::encode($cta); ?>
            </span>
        </div>
    </div>
</a>
