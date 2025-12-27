<?php

use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var string $id
 * @var string $form_id
 * @var string $input_id
 * @var string $results_id
 * @var string $spinner_id
 * @var string $error_id
 * @var string $endpoint
 * @var string $param_name
 * @var string|null $value
 * @var int|null $selected_category_id
 * @var string $placeholder
 * @var string $aria_label
 * @var string $type
 * @var int|null $parent_id
 * @var int $per_page
 * @var int $debounce_ms
 * @var string $load_more_id
 * @var string $categories_id
 * @var \app\models\Category[] $categories
 */
?>
<div class="row justify-content-center">
    <div id="<?= Html::encode($id) ?>"
         class="search-widget col-12 col-lg-10 my-4"
         data-search="1"
         data-endpoint="<?= Html::encode($endpoint) ?>"
         data-param="<?= Html::encode($param_name) ?>"
         data-type="<?= Html::encode($type) ?>"
         data-parent-id="<?= $parent_id === null ? '' : (int)$parent_id ?>"
         data-per-page="<?= $per_page ?>"
         data-debounce="<?= $debounce_ms ?>"
         data-form-id="<?= Html::encode($form_id) ?>"
         data-input-id="<?= Html::encode($input_id) ?>"
         data-results-id="<?= Html::encode($results_id) ?>"
         data-spinner-id="<?= Html::encode($spinner_id) ?>"
         data-error-id="<?= Html::encode($error_id) ?>"
         data-load-more-id="<?= Html::encode($load_more_id) ?>"
         data-categories-id="<?= Html::encode($categories_id) ?>"
         data-selected-category-id="<?= Html::encode($selected_category_id) ?>">

        <form id="<?= Html::encode($form_id) ?>" class="position-relative" action="<?= Html::encode($endpoint) ?>"
              method="GET" role="search" novalidate>
            <div class="mb-3 position-relative">
                <?= Html::input('text', $param_name, $value ?? '', [
                        'id' => $input_id,
                        'class' => 'form-control form-control-lg',
                        'placeholder' => $placeholder,
                        'aria-label' => $aria_label,
                        'autocomplete' => 'off',
                ]) ?>
                <div id="<?= Html::encode($spinner_id) ?>"
                     class="position-absolute top-50 end-0 translate-middle-y me-3 spinner-border text-secondary d-none"
                     role="status" aria-hidden="true" style="width:1.5rem;height:1.5rem;"></div>
            </div>
        </form>

        <?php if (!empty($categories)): ?>
            <div id="<?= Html::encode($categories_id) ?>" class="mb-4 d-flex flex-wrap gap-2">
                <?php foreach ($categories as $category): ?>
                    <?php $isActive = (int)$selected_category_id === (int)$category->id; ?>
                    <button type="button"
                            class="btn btn-sm <?= $isActive ? 'btn-secondary' : 'btn-outline-secondary' ?> rounded-pill"
                            data-category-id="<?= $category->id ?>">
                        <?= Html::encode($category->name) ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div id="<?= Html::encode($results_id) ?>" class="search-results"
             data-empty="<?= Html::encode(Yii::t('app', 'Type at least 2 characters to search…')) ?>"></div>

        <div id="<?= Html::encode($error_id) ?>" class="mt-2 d-none"></div>

        <div class="d-grid mt-3">
            <button type="button"
                    id="<?= Html::encode($load_more_id) ?>"
                    class="btn btn-outline-primary d-none"
                    data-role="search-load-more"
                    aria-label="<?= Html::encode(Yii::t('app', 'Load more results')) ?>">
                <?= Html::encode(Yii::t('app', 'Load more results')) ?>
            </button>
        </div>
    </div>
</div>
