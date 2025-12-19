<?php

namespace app\components;

use app\models\forms\SearchForm;
use Yii;
use yii\helpers\Url;

class Searcher
{
    public function search(SearchForm $form)
    {
        $sql = "SELECT *
FROM (
     SELECT
        'static'::text AS type,
        sc.key AS title,
        sc.slug AS slug,
        sc.content AS snippet,
        word_similarity(:q, sc.searchable_text) as sml
     FROM static_content sc
     WHERE :q <% sc.searchable_text
     UNION ALL
     SELECT
        'course'::text AS type,
        cn.name AS title,
        cn.slug AS slug,
        cn.summary AS snippet,
        word_similarity(:q, cn.searchable_text) AS sml
     FROM course_node cn
     WHERE :q <% cn.searchable_text
     UNION ALL
     SELECT
        'teacher'::text AS type,
        t.full_name AS title,
        t.slug AS slug,
        t.description AS snippet,
        word_similarity(:q, t.searchable_text) AS sml
     FROM teacher t
     WHERE :q <% t.searchable_text
) q
ORDER BY sml DESC, title
LIMIT :limit OFFSET :offset";

        $params[':q'] = $form->q;
        $params[':limit'] = $form->per_page + 1;
        $params[':offset'] = $form->getOffset();

        $rows = Yii::$app->db->createCommand($sql, $params)->queryAll();

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
}