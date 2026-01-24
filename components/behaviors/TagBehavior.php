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

    /**
     * @var string|null The name of the attribute that should be automatically added as a tag.
     */
    public ?string $autoTagAttribute = null;

    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'loadTags',
            ActiveRecord::EVENT_AFTER_INSERT => 'saveTags',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveTags',
        ];
    }

    public function beforeSave(): void
    {
        if ($this->autoTagAttribute !== null) {
            $autoTagValue = $this->owner->{$this->autoTagAttribute};
            if (!empty($autoTagValue)) {
                $tags = $this->owner->{$this->tagAttribute};
                $tagNames = !empty($tags) ? array_map('trim', explode(',', $tags)) : [];

                // Remove old value if it changed
                if (!$this->owner->isNewRecord) {
                    $oldValue = null;
                    if ($this->owner->hasAttribute($this->autoTagAttribute)) {
                        $oldValue = $this->owner->getOldAttribute($this->autoTagAttribute);
                    } else {
                        $oldMethod = 'getOld' . ucfirst($this->autoTagAttribute);
                        if (method_exists($this->owner, $oldMethod)) {
                            $oldValue = $this->owner->$oldMethod();
                        }
                    }

                    if ($oldValue && strcasecmp($oldValue, $autoTagValue) !== 0) {
                        $tagNames = array_filter($tagNames, fn($t) => strcasecmp($t, $oldValue) !== 0);
                    }
                }

                // Add new value if not present (case-insensitive check)
                $found = false;
                foreach ($tagNames as $tn) {
                    if (strcasecmp($tn, $autoTagValue) === 0) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $tagNames[] = $autoTagValue;
                }

                $this->owner->{$this->tagAttribute} = implode(', ', $tagNames);
            }
        }
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
        if ($this->autoTagAttribute !== null) {
            $autoTagValue = $this->owner->{$this->autoTagAttribute};
            if (!empty($autoTagValue)) {
                $tags = $this->owner->{$this->tagAttribute};
                $tagNames = !empty($tags) ? array_map('trim', explode(',', $tags)) : [];

                // Remove old value if it changed
                if (!$this->owner->isNewRecord) {
                    $oldValue = null;
                    if ($this->owner->hasAttribute($this->autoTagAttribute)) {
                        $oldValue = $this->owner->getOldAttribute($this->autoTagAttribute);
                    } else {
                        $oldMethod = 'getOld' . ucfirst($this->autoTagAttribute);
                        if (method_exists($this->owner, $oldMethod)) {
                            $oldValue = $this->owner->$oldMethod();
                        }
                    }

                    if ($oldValue && strcasecmp($oldValue, $autoTagValue) !== 0) {
                        $tagNames = array_filter($tagNames, fn($t) => strcasecmp($t, $oldValue) !== 0);
                    }
                }

                // Add new value if not present (case-insensitive check)
                $found = false;
                foreach ($tagNames as $tn) {
                    if (strcasecmp($tn, $autoTagValue) === 0) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $tagNames[] = $autoTagValue;
                }

                $this->owner->{$this->tagAttribute} = implode(', ', $tagNames);
            }
        }

        $tags = $this->owner->{$this->tagAttribute};
        
        if ($tags === null) {
            return;
        }

        $tagNames = array_filter(array_map('trim', explode(',', $tags)));
        $currentTags = $this->owner->{$this->tagRelation};
        $currentTagNames = ArrayHelper::getColumn($currentTags, $this->tagNameAttribute);

        // Tags to add (case-insensitive)
        $tagsToAdd = [];
        foreach ($tagNames as $tn) {
            $exists = false;
            foreach ($currentTagNames as $ctn) {
                if (strcasecmp($tn, $ctn) === 0) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $tagsToAdd[] = $tn;
            }
        }

        // Tags to remove (case-insensitive)
        $tagsToRemove = [];
        foreach ($currentTagNames as $ctn) {
            $keep = false;
            foreach ($tagNames as $tn) {
                if (strcasecmp($tn, $ctn) === 0) {
                    $keep = true;
                    break;
                }
            }
            if (!$keep) {
                $tagsToRemove[] = $ctn;
            }
        }

        foreach ($tagsToRemove as $tagName) {
            $tag = Tag::findOne([$this->tagNameAttribute => $tagName]);
            if ($tag) {
                $this->owner->unlink($this->tagRelation, $tag, true);
            }
        }

        foreach ($tagsToAdd as $tagName) {
            // Check if tag already exists in DB (maybe with different casing)
            $tag = Tag::find()->where(['ilike', $this->tagNameAttribute, $tagName])->one();
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
