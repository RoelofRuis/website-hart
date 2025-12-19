<?php

namespace app\components\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class SearchableTextBehavior extends Behavior
{
    public array $source_attributes = [];
    public string $targetAttribute = 'searchable_text';

    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'updateSearchableText',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'updateSearchableText',
        ];
    }

    public function updateSearchableText(): void
    {
        /** @var ActiveRecord $model */
        $model = $this->owner;
        $pieces = [];
        foreach ($this->source_attributes as $attr) {
            $val = (string)($model->$attr ?? '');
            if ($val === '') {
                continue;
            }
            $clean = strip_tags($val);
            $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $clean = preg_replace('/\s+/u', ' ', $clean);
            $clean = trim($clean);
            if ($clean !== '') {
                $pieces[] = $clean;
            }
        }
        $model->{$this->targetAttribute} = implode("\n\n", $pieces);
    }
}
