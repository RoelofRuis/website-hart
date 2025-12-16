<?php
/**
 * @var string $name
 * @var string|null $value
 * @var string $inputId
 * @var string $buttonId
 * @var string $label
 * @var string $unlockLabel
 * @var string $tooltip
 * @var array $inputOptions
 * @var bool $renderButton
 */

use yii\bootstrap5\Html;

?>
<div class="mb-3">
    <label class="form-label" for="<?= Html::encode($inputId) ?>"><?= Html::encode($label) ?></label>
    <div class="input-group">
        <?= Html::input('text', $name, $value, $inputOptions) ?>
        <?php if ($renderButton): ?>
            <button type="button"
                    class="btn btn-outline-secondary"
                    id="<?= Html::encode($buttonId) ?>"
                    data-bs-toggle="tooltip"
                    title="<?= Html::encode($tooltip) ?>">
                <?= Html::encode($unlockLabel) ?>
            </button>
        <?php endif; ?>
    </div>
</div>

<?php
$js = <<<JS
(function(){
  var btn = document.getElementById('{$buttonId}');
  var input = document.getElementById('{$inputId}');
  if (!input) return;

  if (btn) {
    // init tooltip if Bootstrap is available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
      try { new bootstrap.Tooltip(btn); } catch (e) {}
    }
    btn.addEventListener('click', function(){
      input.disabled = false;
      input.focus();
      btn.disabled = true;
      btn.title = '';
      btn.classList.add('active');
    });
  }
})();
JS;
$this->registerJs($js);
?>
