<?php

namespace app\components\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Populates a TEXT column with cleaned (HTML-stripped, lowercased, unaccented) text
 * derived from one or more model attributes, to be used for trigram search.
 */
class SearchableTextBehavior extends Behavior
{
    /**
     * @var string[] Attributes to combine as source text.
     */
    public array $sourceAttributes = [];

    /**
     * @var string Target attribute name where cleaned text is stored (e.g., 'searchable_text').
     */
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
        foreach ($this->sourceAttributes as $attr) {
            $val = (string)($model->$attr ?? '');
            if ($val === '') {
                continue;
            }
            // Strip HTML tags, decode entities (basic), normalize whitespace
            $clean = strip_tags($val);
            $clean = html_entity_decode($clean, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $clean = preg_replace('/\s+/u', ' ', $clean);
            $clean = trim((string)$clean);
            if ($clean !== '') {
                $pieces[] = mb_strtolower($clean);
            }
        }
        $model->{$this->targetAttribute} = implode("\n\n", $pieces);
    }
}
