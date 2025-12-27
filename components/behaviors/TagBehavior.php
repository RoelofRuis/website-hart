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
    public string $tagRelation = 'tags';

    /**
     * @var string The name of the tag model's name attribute.
     */
    public string $tagNameAttribute = 'name';

    private ?string $_tags = null;

    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'saveTags',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveTags',
        ];
    }

    public function getTags(): string
    {
        if ($this->_tags !== null) {
            return $this->_tags;
        }

        if ($this->owner->isNewRecord) {
            return '';
        }

        $tags = $this->owner->{$this->tagRelation};
        $tagNames = ArrayHelper::getColumn($tags, $this->tagNameAttribute);
        
        return $this->_tags = implode(', ', $tagNames);
    }

    public function setTags(string $value): void
    {
        $this->_tags = $value;
    }

    public function saveTags(): void
    {
        if ($this->_tags === null) {
            return;
        }

        $tagNames = array_filter(array_map('trim', explode(',', $this->_tags)));
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

    public function __get($name)
    {
        if ($name === $this->tagAttribute) {
            return $this->getTags();
        }
        return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if ($name === $this->tagAttribute) {
            $this->setTags($value);
        } else {
            parent::__set($name, $value);
        }
    }
}
