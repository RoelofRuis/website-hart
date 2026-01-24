<?php

namespace app\models\forms;

use yii\base\Model;

class SearchForm extends Model
{
    public string $q = '';
    public string $type = '';
    public ?int $category_id = null;
    public int $per_page = 12;
    public int $page = 1;

    public function rules(): array
    {
        return [
            ['q', 'string', 'max' => 255],
            ['type', 'in', 'range' => ['all', 'courses', 'teachers', 'subcourses']],
            [['parent_id', 'category_id'], 'integer'],
            [['per_page'], 'integer', 'min' => 1, 'max' => 30],
            [['page'], 'integer', 'min' => 1],
        ];
    }

    public function getTrimmedQuery(): string
    {
        return '%' . trim($this->q) . '%';
    }

    public function hasEmptyQuery(): bool
    {
        return empty(trim($this->q));
    }

    public function getOffset(): int
    {
        return ($this->page - 1) * $this->per_page;
    }
}