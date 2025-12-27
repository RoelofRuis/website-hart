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
            Yii::$app->db->createCommand("SELECT setseed(:seed)", [':seed' => $this->getSearchSeed()])->execute();
            $query->orderBy([
                new Expression('CASE WHEN type = \'static\' THEN 1 ELSE 0 END'),
                new Expression('RANDOM()')
            ]);
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
            ->from(['cn' => '{{%course}}']);

        if ($form->category_id !== null) {
            $subquery->andWhere(['cn.category_id' => $form->category_id]);
        }

        if ($form->hasEmptyQuery()) {
            $subquery
                ->addSelect([
                    new Expression("1 AS rank"),
                    'cn.summary AS snippet'
                ]);
        } else {
            $subquery
                ->addSelect([
                    new Expression("1 AS rank"),
                    'cn.summary AS snippet'
                ])
                ->innerJoin(['ct' => '{{%course_tag}}'], 'ct.course_id = cn.id')
                ->innerJoin(['t' => '{{%tag}}'], 't.id = ct.tag_id')
                ->andWhere(['LIKE', 't.name', new Expression(':q')]);
        }

        return $subquery;
    }

    private function buildTeacherSubquery(SearchForm $form): Query
    {
        $subquery = (new Query())
            ->select([
                new Expression("'teacher'::text AS type"),
                'u.full_name AS title',
                't.slug AS slug',
                't.profile_picture AS image',
            ])
            ->from(['t' => '{{%teacher}}'])
            ->innerJoin(['u' => '{{%user}}'], 't.user_id = u.id');

        if ($form->hasEmptyQuery()) {
            $subquery
                ->addSelect([
                    new Expression("1 AS rank"),
                    't.description AS snippet'
                ]);
        } else {
            $subquery
                ->addSelect([
                    new Expression("1 AS rank"),
                    't.description AS snippet'
                ])
                ->innerJoin(['tt' => '{{%teacher_tag}}'], 'tt.teacher_id = t.id')
                ->innerJoin(['tg' => '{{%tag}}'], 'tg.id = tt.tag_id')
                ->andWhere([
                    'OR',
                    ['LIKE', 'tg.name', new Expression(':q')],
                    ['LIKE', 'u.full_name', new Expression(':q')]
                ]);
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
            ->from(['sc' => '{{%static_content}}']);

        if ($form->hasEmptyQuery()) {
            $subquery->addSelect([
                new Expression('0 AS rank'),
                'sc.summary AS snippet',
            ]);
        } else {
            $subquery
                ->addSelect([
                    new Expression('0 AS rank'),
                    'sc.summary AS snippet',
                ])
                ->innerJoin(['sct' => '{{%static_content_tag}}'], 'sct.static_content_id = sc.id')
                ->innerJoin(['tg' => '{{%tag}}'], 'tg.id = sct.tag_id')
                ->andWhere(['LIKE', 'tg.name', new Expression(':q')]);
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