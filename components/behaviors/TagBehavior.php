<?php

namespace app\components\behaviors;

use app\models\Tag;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * @property ActiveRecord $owner
 */
class TagBehavior extends Behavior
{
    /**
     * @var string The name of the attribute that will store the tags as a comma-separated string.
     */
    public string $tagAttribute = 'tags';

    /**
     * @var string The name of the relation to the tags.
     */
    public string $tagRelation = 'tagsRelation';

    /**
     * @var string The name of the tag model's name attribute.
     */
    public string $tagNameAttribute = 'name';

    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'loadTags',
            ActiveRecord::EVENT_AFTER_INSERT => 'saveTags',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveTags',
        ];
    }

    public function loadTags(): void
    {
        if ($this->owner->{$this->tagAttribute} !== null) {
            return;
        }

        if ($this->owner->isNewRecord) {
            return;
        }

        $tags = $this->owner->{$this->tagRelation};
        $tagNames = ArrayHelper::getColumn($tags, $this->tagNameAttribute);

        $this->owner->{$this->tagAttribute} = implode(', ', $tagNames);
    }

    public function saveTags(): void
    {
        $tags = $this->owner->{$this->tagAttribute};

        if ($tags === null) {
            return;
        }

        $tagNames = array_filter(array_map('trim', explode(',', $tags)));
        $currentTags = $this->owner->{$this->tagRelation};
        $currentTagNames = ArrayHelper::getColumn($currentTags, $this->tagNameAttribute);

        // Tags to add
        $tagsToAdd = array_diff($tagNames, $currentTagNames);
        // Tags to remove
        $tagsToRemove = array_diff($currentTagNames, $tagNames);

        foreach ($tagsToRemove as $tagName) {
            $tag = Tag::findOne([$this->tagNameAttribute => $tagName]);
            if ($tag) {
                $this->owner->unlink($this->tagRelation, $tag, true);
            }
        }

        foreach ($tagsToAdd as $tagName) {
            $tag = Tag::findOne([$this->tagNameAttribute => $tagName]);
            if (!$tag) {
                $tag = new Tag();
                $tag->{$this->tagNameAttribute} = $tagName;
                $tag->save();
            }
            $this->owner->link($this->tagRelation, $tag);
        }
    }

    public function canGetProperty($name, $checkVars = true): bool
    {
        return $name === $this->tagAttribute || parent::canGetProperty($name, $checkVars);
    }

    public function canSetProperty($name, $checkVars = true): bool
    {
        return $name === $this->tagAttribute || parent::canSetProperty($name, $checkVars);
    }
}
