<?php
/** @var yii\web\View $this */
/** @var array<int,array<string,mixed>> $items */

use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Messages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teachers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-messages">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (empty($items)): ?>
        <div class="text-muted"><?= Html::encode(Yii::t('app', 'No messages found.')) ?></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                <tr>
                    <th><?= Html::encode(Yii::t('app', 'Created At')) ?></th>
                    <th><?= Html::encode(Yii::t('app', 'Type')) ?></th>
                    <th><?= Html::encode(Yii::t('app', 'Course')) ?></th>
                    <th><?= Html::encode(Yii::t('app', 'Name')) ?></th>
                    <th><?= Html::encode(Yii::t('app', 'Email')) ?></th>
                    <th><?= Html::encode(Yii::t('app', 'Telephone')) ?></th>
                    <th><?= Html::encode(Yii::t('app', 'Message')) ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($items as $row): ?>
                    <tr>
                        <td><?= Yii::$app->formatter->asDatetime($row['created_at']) ?></td>
                        <td>
                            <?php if ($row['type'] === 'signup'): ?>
                                <span class="badge bg-primary"><?= Html::encode(Yii::t('app', 'Signup')) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= Html::encode(Yii::t('app', 'Contact')) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= Html::encode($row['course'] ?? '') ?></td>
                        <td><?= Html::encode($row['from_name'] ?? '') ?></td>
                        <td>
                            <?php if (!empty($row['email'])): ?>
                                <a href="mailto:<?= Html::encode($row['email']) ?>"><?= Html::encode($row['email']) ?></a>
                            <?php endif; ?>
                        </td>
                        <td><?= Html::encode($row['telephone'] ?? '') ?></td>
                        <td>
                            <?php if ($row['type'] === 'signup' && $row['age'] !== null): ?>
                                <?= Html::encode(Yii::t('app', 'Student Age')) ?>: <?= Html::encode((string)$row['age']) ?>
                            <?php elseif (!empty($row['message'])): ?>
                                <?= nl2br(Html::encode($row['message'])) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
