<?php

namespace app\models;

use app\components\FileDataStore;
use yii\base\Model;

/**
 * Base class for simple file-backed models using JSON arrays.
 */
abstract class BaseFileModel extends Model
{
    /** @return string file name inside @app/data directory */
    abstract protected static function fileName(): string;

    /** The primary key attribute name (defaults to 'id'). */
    protected static function pk(): string { return 'id'; }

    /**
     * @return static[]
     */
    public static function findAll(): array
    {
        $rows = FileDataStore::load(static::fileName());
        $items = [];
        foreach ($rows as $row) {
            $m = new static();
            $m->setAttributes($row, false);
            $items[] = $m;
        }
        return $items;
    }

    /**
     * @param mixed $value
     * @param string|null $by attribute name or null for pk
     * @return static|null
     */
    public static function findOne($value, ?string $by = null)
    {
        $by = $by ?: static::pk();
        $rows = FileDataStore::load(static::fileName());
        foreach ($rows as $row) {
            if ((string)($row[$by] ?? '') === (string)$value) {
                $m = new static();
                $m->setAttributes($row, false);
                return $m;
            }
        }
        return null;
    }
}
