<?php
/** @var yii\web\View $this */
/** @var ContactMessage[] $messages */

use app\models\ContactMessage;
use yii\bootstrap5\Html;

$this->title = Yii::t('app', 'Messages');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Teacher Dashboard'), 'url' => ['site/manage']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="teacher-messages">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (empty($messages)): ?>
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
                <?php foreach ($messages as $message): ?>
                    <tr>
                        <td><?= Yii::$app->formatter->asDatetime($message->created_at) ?></td>
                        <td>
                            <?php if ($message->type === 'signup'): ?>
                                <span class="badge bg-primary"><?= Html::encode(Yii::t('app', 'Signup')) ?></span>
                            <?php elseif ($message->type === 'trial'): ?>
                                <span class="badge bg-success"><?= Html::encode(Yii::t('app', 'Trial')) ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><?= Html::encode(Yii::t('app', 'Contact')) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($message->lessonFormat): ?>
                                <?= Html::encode($message->lessonFormat->course->name) ?>
                                <br/>
                                <small class="text-muted">
                                    <?= Html::encode($message->lessonFormat->getFormattedDescription()) ?>
                                </small>
                            <?php else: ?>
                                <span class="text-muted"><?= Html::encode(Yii::t('app', 'Direct Contact')) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= Html::encode($message->name) ?></td>
                        <td>
                            <?php if (!empty($message->email)): ?>
                                <a href="mailto:<?= Html::encode($message->email) ?>"><?= Html::encode($message->email) ?></a>
                            <?php endif; ?>
                        </td>
                        <td><?= Html::encode($message->telephone) ?></td>
                        <td>
                            <?php if (($message->type === 'signup' || $message->type === 'trial') && $message->age !== null): ?>
                                <?= Html::encode(Yii::t('app', 'Student Age')) ?>: <?= Html::encode((string)$message->age) ?>
                            <?php elseif (!empty($message->message)): ?>
                                <?= nl2br(Html::encode($message->message)) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
