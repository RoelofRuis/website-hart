<?php

use yii\bootstrap5\Html;

/**
 * @var yii\web\View $this
 * @var string $formId
 * @var string $inputId
 * @var string $placeholder
 * @var string|null $value
 * @var string $paramName
 * @var string $action
 * @var string $method
 */
?>
<form id="<?= Html::encode($formId) ?>"
      class="row gy-2 gx-2 align-items-center mb-4"
      method="GET"
      action="<?= Html::encode($action) ?>">
    <div class="col-sm-10">
        <?= Html::input('text', $paramName, $value ?? '', [
                'id' => $inputId,
                'class' => 'form-control',
                'placeholder' => $placeholder,
        ]) ?>
    </div>
    <div class="col-sm-2 d-grid">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
    </div>
</form>
