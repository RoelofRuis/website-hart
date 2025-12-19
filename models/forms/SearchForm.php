<?php

namespace app\models\forms;

use yii\base\Model;

class SearchForm extends Model
{
    public string $q = '';
    public string $type = '';
    public ?int $parent_id = null;
    public int $per_page = 12;
    public int $page = 1;
    public bool $suppress_empty = false;

    public function rules(): array
    {
        return [
            [['suppress_empty'], 'boolean'],
            ['q', 'string', 'max' => 255],
            ['type', 'in', 'range' => ['all', 'courses', 'teachers', 'subcourses']],
            [['parent_id'], 'integer'],
            [['per_page'], 'integer', 'min' => 1, 'max' => 30],
            [['page'], 'integer', 'min' => 1],
        ];
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->per_page;
    }
}