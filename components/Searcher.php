<?php

namespace app\components;

use app\models\forms\SearchForm;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Url;

class Searcher
{
    public function search(SearchForm $form)
    {
        $data_query = new Query();

        if ($form->type === 'courses') {
            $data_query = $this->buildCourseSubquery();
        } elseif ($form->type === 'subcourses') {
            $data_query = $this->buildCourseSubquery()
                ->andWhere(['parent_id' => $form->parent_id]);
        } elseif ($form->type === 'teachers') {
            $data_query = $this->buildTeacherSubquery();
        } elseif ($form->type === 'all') {
            $data_query = $this->buildStaticSubquery()
                ->union($this->buildCourseSubquery(), true)
                ->union($this->buildTeacherSubquery(), true);
        }

        $query = (new Query())
            ->select("*")
            ->from(['data' => $data_query])
            ->addParams([':q' => $form->q])
            ->orderBy(['sml' =>  SORT_DESC, 'title' => SORT_ASC])
            ->limit($form->per_page + 1)
            ->offset($form->getOffset());

        $rows = $query->all();

        $results = [];
        $has_more = false;

        if (count($rows) > $form->per_page) {
            $has_more = true;
            array_pop($rows);
        }
        foreach ($rows as $row) {
            $type = (string)$row['type'];
            $title = (string)$row['title'];
            $slug = (string)$row['slug'];
            $rank = (float)$row['sml'];
            $snippet = (string)($row['snippet'] ?? '');
            $image = isset($row['image']) ? (string)$row['image'] : null;

            if ($type === 'course') {
                $url = Url::to(['course/view', 'slug' => $slug]);
            } elseif ($type === 'teacher') {
                $url = Url::to(['teacher/view', 'slug' => $slug]);
            } elseif ($type === 'static') {
                $url = Url::to(['static/' . $slug]);
            } else {
                $url = '#';
            }

            $results[] = [
                'type' => $type,
                'title' => $title,
                'url' => $url,
                'snippet' => $snippet,
                'image' => $image,
                'rank' => $rank,
            ];
        }

        return [$results, $has_more];
    }

    private function buildCourseSubquery(): Query
    {
        return (new Query())
            ->select([
                new Expression("'course'::text AS type"),
                'cn.name AS title',
                'cn.slug AS slug',
                'word_similarity(:q, cn.searchable_text) as sml',
                new Expression("ts_headline('simple', cn.searchable_text, plainto_tsquery('simple', :q), 'MaxWords=30, MinWords=10, ShortWord=2') AS snippet"),
            ])
            ->from(['cn' => '{{%course_node}}'])
            ->where(':q <% cn.searchable_text');
    }

    private function buildTeacherSubquery(): Query
    {
        return (new Query())
            ->select([
                new Expression("'teacher'::text AS type"),
                't.full_name AS title',
                't.slug AS slug',
                'word_similarity(:q, t.searchable_text) as sml',
                new Expression("ts_headline('simple', t.searchable_text, plainto_tsquery('simple', :q), 'MaxWords=30, MinWords=10, ShortWord=2') AS snippet"),
            ])
            ->from(['t' => '{{%teacher}}'])
            ->where(':q <% t.searchable_text');
    }

    private function buildStaticSubquery(): Query
    {
        return (new Query())
            ->select([
                new Expression("'teacher'::text AS type"),
                'sc.key AS title',
                'sc.slug AS slug',
                'word_similarity(:q, sc.searchable_text) as sml',
                new Expression("ts_headline('simple', sc.searchable_text, plainto_tsquery('simple', :q), 'MaxWords=30, MinWords=10, ShortWord=2') AS snippet"),
            ])
            ->from(['sc' => '{{%static_content}}'])
            ->where(':q <% sc.searchable_text');
    }
}