<?php

namespace app\components\behaviors;

use app\models\Changelog;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\web\Application;

class ChangelogBehavior extends Behavior
{
    /** @var array attributes to exclude from changelog */
    public $excludeAttributes = [];

    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterUpdate',
        ];
    }

    public function afterUpdate($event): void
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $changedAttributes = $event->changedAttributes;

        $changes = [];
        foreach ($changedAttributes as $name => $oldValue) {
            if (in_array($name, $this->excludeAttributes)) {
                continue;
            }

            $newValue = $owner->getAttribute($name);

            // Loose comparison to avoid issues with different types from DB vs PHP
            if ($oldValue != $newValue) {
                $changes[$name] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            $log = new Changelog();
            $log->model_class = get_class($owner);
            $log->model_id = implode('-', (array)$owner->getPrimaryKey());
            $log->changed_by = Yii::$app instanceof Application && !Yii::$app->user->isGuest
                ? Yii::$app->user->id
                : null;
            $log->changes = Json::encode($changes);
            $log->save(false);
        }
    }
}
