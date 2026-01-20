<?php

/** @var yii\web\View $this */
/** @var StaticContent $model */

use app\models\StaticContent;
use yii\bootstrap5\Html;
use app\widgets\ContactFormWidget;
use app\models\ContactMessage;
use yii\helpers\HtmlPurifier;

$this->title = $model->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row site-static">
    <div class="col-lg-7 col-xl-8">
        <h1 class="mb-3"><?= Html::encode($this->title) ?></h1>
        <?= HtmlPurifier::process($model->content); ?>

        <?php if ($model->updated_at): ?>
            <p class="text-muted small mt-4">
                <?= Yii::t('app', 'Last updated: {date}', ['date' => Yii::$app->formatter->asDate($model->updated_at)]) ?>
            </p>
        <?php endif; ?>
    </div>
    <div class="col-lg-5 col-xl-4">
        <?= ContactFormWidget::widget([
            'heading' => Yii::t('app', 'Contact the board'),
            'reasons' => [
                ContactMessage::TYPE_ORGANISATION_CONTACT => Yii::t('app', 'Contact the board'),
            ],
        ]) ?>
    </div>
</div>
