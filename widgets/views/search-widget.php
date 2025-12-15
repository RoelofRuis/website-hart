<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var string $id
 * @var string $formId
 * @var string $inputId
 * @var string $resultsId
 * @var string $spinnerId
 * @var string $endpoint
 * @var string $paramName
 * @var string|null $value
 * @var string $placeholder
 * @var string $ariaLabel
 */
?>
<div id="<?= Html::encode($id) ?>" class="hart-search-widget my-4">
    <form id="<?= Html::encode($formId) ?>" class="position-relative" action="<?= Html::encode($endpoint) ?>"
          method="GET" role="search">
        <div class="mb-3 position-relative">
            <?= Html::input('text', $paramName, $value ?? '', [
                    'id' => $inputId,
                    'class' => 'form-control form-control-lg py-3 px-4',
                    'placeholder' => $placeholder,
                    'aria-label' => $ariaLabel,
                    'autocomplete' => 'off',
            ]) ?>
            <div id="<?= Html::encode($spinnerId) ?>"
                 class="position-absolute top-50 end-0 translate-middle-y me-3 spinner-border text-secondary d-none"
                 role="status" aria-hidden="true" style="width:1.5rem;height:1.5rem;"></div>
        </div>
    </form>

    <div id="<?= Html::encode($resultsId) ?>" class="hart-search-results"
         data-empty="<?= Html::encode(Yii::t('app', 'Type at least 2 characters to search…')) ?>"></div>
</div>
