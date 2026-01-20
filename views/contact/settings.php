<?php

use app\models\User;
use yii\bootstrap5\Html;
use yii\helpers\ArrayHelper;

/**
 * @var yii\web\View $this
 * @var array $types
 * @var User[] $users
 * @var array $selected
 */

$this->title = Yii::t('app', 'Contactinstellingen');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = $this->title;

$userList = ArrayHelper::map($users, fn($u) => $u->id, fn($u) => $u->full_name);
?>

<div class="contact-settings">
    <h1><?= Html::encode($this->title) ?></h1>
    <p><?= Yii::t('app', 'Select which users should receive notifications for general contact message types.') ?></p>

    <form method="post">
    <input type="hidden" name="<?= Yii::$app->request->csrfParam ?>" value="<?= Yii::$app->request->csrfToken ?>">
    <?php foreach ($types as $type => $label): ?>
        <div class="card mt-4">
            <div class="card-body">
                <label class="form-label fw-bold"><?= Html::encode($label) ?></label>
                <div class="row ps-3">
                    <?php foreach ($userList as $userId => $userName): ?>
                        <div class="col-md-4 col-lg-3 form-check">
                            <input type="checkbox" class="form-check-input"
                                   name="ContactTypeReceiver[<?= $type ?>][]"
                                   value="<?= $userId ?>"
                                   id="chk-<?= $type ?>-<?= $userId ?>"
                                   <?= in_array($userId, $selected[$type] ?? []) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="chk-<?= $type ?>-<?= $userId ?>">
                                <?= Html::encode($userName) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                    <input type="hidden" name="ContactTypeReceiver[<?= $type ?>][]" value="">
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <div class="form-group mt-4">
            <?= Html::submitButton(Yii::t('app', 'Instellingen opslaan'), ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Annuleren'), ['site/manage'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </form>
</div>
