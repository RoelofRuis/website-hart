<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var string $id
 * @var string $formId
 * @var string $inputId
 * @var string $resultsId
 * @var string $spinnerId
 * @var string $errorId
 * @var string $loadMoreId
 * @var string $endpoint
 * @var string $paramName
 * @var string|null $value
 * @var string $placeholder
 * @var string $ariaLabel
 * @var string $type
 * @var int|null $parentId
 * @var int $perPage
 * @var int $debounceMs
 */
?>
<div id="<?= Html::encode($id) ?>" class="hart-search-widget my-4"
     data-hart-search="1"
     data-endpoint="<?= Html::encode($endpoint) ?>"
     data-param="<?= Html::encode($paramName) ?>"
     data-type="<?= Html::encode($type) ?>"
     data-parent-id="<?= $parentId === null ? '' : (int)$parentId ?>"
     data-per-page="<?= (int)$perPage ?>"
     data-debounce="<?= (int)$debounceMs ?>"
     data-form-id="<?= Html::encode($formId) ?>"
     data-input-id="<?= Html::encode($inputId) ?>"
     data-results-id="<?= Html::encode($resultsId) ?>"
     data-spinner-id="<?= Html::encode($spinnerId) ?>"
     data-error-id="<?= Html::encode($errorId) ?>"
     data-load-more-id="<?= Html::encode($loadMoreId) ?>">

    <form id="<?= Html::encode($formId) ?>" class="position-relative" action="<?= Html::encode($endpoint) ?>"
          method="GET" role="search" novalidate>
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

    <div id="<?= Html::encode($errorId) ?>" class="mt-2 d-none"></div>
    <div class="text-center mt-3">
        <button id="<?= Html::encode($loadMoreId) ?>" type="button" class="btn btn-outline-secondary d-none">
            <?= Html::encode(Yii::t('app', 'Load more')) ?>
        </button>
    </div>
</div>
