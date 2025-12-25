<?php

namespace app\components;

use app\models\forms\SearchForm;
use Yii;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\Url;

class Searcher
{
    const SEARCH_SEED = 'search_seed';

    public function search(SearchForm $form): SearchResult
    {
        $data_query = new Query();

        if ($form->type === 'courses') {
            $data_query = $this->buildCourseSubquery($form);
        } elseif ($form->type === 'subcourses') {
            $data_query = $this->buildCourseSubquery($form)
                ->andWhere(['parent_id' => $form->parent_id]);
        } elseif ($form->type === 'teachers') {
            $data_query = $this->buildTeacherSubquery($form);
        } elseif ($form->type === 'all') {
            $data_query = $this->buildStaticSubquery($form)
                ->union($this->buildCourseSubquery($form), true)
                ->union($this->buildTeacherSubquery($form), true);
        }

        $query = (new Query())
            ->select("*")
            ->from(['data' => $data_query])
            ->limit($form->per_page + 1)
            ->offset($form->getOffset());

        if ($form->hasEmptyQuery()) {
            Yii::$app->db->createCommand("SELECT setseed(:seed)", [':seed' => $this->getSearchSeed()])->execute();
            $query->orderBy(new Expression('RANDOM()'));
        } else {
            $query->orderBy(['rank' => SORT_DESC, 'title' => SORT_ASC]);
        }

        if (!$form->hasEmptyQuery()) {
            $query->addParams([':q' => $form->getTrimmedQuery()]);
        }

        $rows = $query->all();

        $results = [];
        $has_next_page = false;

        if (count($rows) > $form->per_page) {
            $has_next_page = true;
            array_pop($rows);
        }
        foreach ($rows as $row) {
            $type = (string)$row['type'];
            $title = (string)$row['title'];
            $slug = (string)$row['slug'];
            $rank = (float)$row['rank'];
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

        $next_page = $has_next_page ? ($form->page + 1) : null;

        return new SearchResult($results, $has_next_page, $next_page, $form->q);
    }

    private function buildCourseSubquery(SearchForm $form): Query
    {
        $subquery = (new Query())
            ->select([
                new Expression("'course'::text AS type"),
                'cn.name AS title',
                'cn.slug AS slug',
                'cn.cover_image AS image',
            ])
            ->from(['cn' => '{{%course_node}}']);

        if ($form->hasEmptyQuery()) {
            $subquery
                ->addSelect([
                    new Expression("1 AS rank"),
                    'cn.summary AS snippet'
                ]);
        } else {
            $subquery
                ->addSelect([
                    'word_similarity(:q, cn.searchable_text) as rank',
                    new Expression("ts_headline('simple', cn.searchable_text, plainto_tsquery('simple', :q), 'MaxWords=30, MinWords=10, ShortWord=2') AS snippet"),
                ])
                ->andWhere(':q <% cn.searchable_text');
        }

        return $subquery;
    }

    private function buildTeacherSubquery(SearchForm $form): Query
    {
        $subquery = (new Query())
            ->select([
                new Expression("'teacher'::text AS type"),
                't.full_name AS title',
                't.slug AS slug',
                't.profile_picture AS image',
            ])
            ->from(['t' => '{{%teacher}}']);

        if ($form->hasEmptyQuery()) {
            $subquery
                ->addSelect([
                    new Expression("1 AS rank"),
                    't.description AS snippet'
                ]);
        } else {
            $subquery
                ->addSelect([
                    'word_similarity(:q, t.searchable_text) as rank',
                    new Expression("ts_headline('simple', t.searchable_text, plainto_tsquery('simple', :q), 'MaxWords=30, MinWords=10, ShortWord=2') AS snippet"),
                ])
                ->andWhere(':q <% t.searchable_text');
        }

        return $subquery;
    }

    private function buildStaticSubquery(SearchForm $form): Query
    {
        $subquery = (new Query())
            ->select([
                new Expression("'static'::text AS type"),
                'sc.title AS title',
                'sc.slug AS slug',
                'sc.cover_image AS image',
            ])
            ->from(['sc' => '{{%static_content}}'])
            ->andWhere(['sc.is_searchable' => true]);

        if ($form->hasEmptyQuery()) {
            $subquery->addSelect([
                new Expression('0 AS rank'),
                'sc.summary AS snippet',
            ]);
        } else {
            $subquery
                ->addSelect([
                    'word_similarity(:q, sc.searchable_text) as rank',
                    new Expression("ts_headline('simple', sc.searchable_text, plainto_tsquery('simple', :q), 'MaxWords=30, MinWords=10, ShortWord=2') AS snippet"),
                ])
                ->andWhere(':q <% sc.searchable_text');
        }

        return $subquery;
    }

    private function getSearchSeed(): float
    {
        $seed = Yii::$app->session->get(self::SEARCH_SEED, null);
        if ($seed === null) {
            $seed = mt_rand() / mt_getrandmax();
            Yii::$app->session->set(self::SEARCH_SEED, $seed);
        }
        return (float)$seed;
    }
}