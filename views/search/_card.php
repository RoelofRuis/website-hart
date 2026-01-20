<?php

/**
 * @var string $href
 * @var string $image
 * @var string $title
 * @var string $content
 * @var string $cta
 */

use yii\helpers\Html;

?>
<a
    href="<?= Html::encode($href) ?>"
    class="text-decoration-none text-reset"
>
    <div class="card h-100 lift-card">
        <?php if (!empty($image)): ?>
            <?= Html::img($image, [
                'class' => 'card-img-top',
                'alt' => Html::encode($title),
                'style' => 'aspect-ratio: 16/9; object-fit: cover;',
            ]) ?>
        <?php endif; ?>
        <div class="card-body">
            <h5 class="card-title"><?= Html::encode($title); ?></h5>
            <p class="card-text text-muted">
                <?= Html::encode(strip_tags($content)); ?>
            </p>
        </div>
        <div class="card-footer p-0">
            <span class="btn btn-outline-primary w-100 rounded-0 rounded-bottom" aria-hidden="true">
                <?= Html::encode($cta); ?>
            </span>
        </div>
    </div>
</a>
