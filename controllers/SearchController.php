<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;

class SearchController extends Controller
{
    public function actionIndex(string $q = null)
    {
        $q = $q ?? Yii::$app->request->get('q');

        $results = [];
        $qNorm = trim((string)$q);
        if ($qNorm !== '' && mb_strlen($qNorm) >= 2) {
            $db = Yii::$app->db;

            // Full-text search across course_node, teacher, and static_content using their search_vector columns.
            // Use websearch_to_tsquery for intuitive search terms, unaccent for diacritics-insensitive matching.
            $sql = "
                WITH query AS (
                    SELECT websearch_to_tsquery('dutch', unaccent(:q)) AS q
                )
                SELECT type, title, slug, rank, snippet
                FROM (
                    SELECT 'course'::text AS type,
                           c.name AS title,
                           c.slug AS slug,
                           ts_rank_cd(c.search_vector, query.q, 32) AS rank,
                           ts_headline('dutch', COALESCE(c.summary, ''), query.q,
                                       'ShortWord=3, MaxFragments=2, MinWords=5, MaxWords=12, HighlightAll=FALSE') AS snippet
                    FROM {{%course_node}} c, query
                    WHERE c.search_vector @@ query.q
                    UNION ALL
                    SELECT 'teacher'::text AS type,
                           t.full_name AS title,
                           t.slug AS slug,
                           ts_rank_cd(t.search_vector, query.q, 32) AS rank,
                           ts_headline('dutch', COALESCE(t.description, ''), query.q,
                                       'ShortWord=3, MaxFragments=2, MinWords=5, MaxWords=12, HighlightAll=FALSE') AS snippet
                    FROM {{%teacher}} t, query
                    WHERE t.search_vector @@ query.q
                    UNION ALL
                    SELECT 'static'::text AS type,
                           s.key AS title,
                           s.slug AS slug,
                           ts_rank_cd(s.search_vector, query.q, 32) AS rank,
                           ts_headline('dutch', COALESCE(s.content, ''), query.q,
                                       'ShortWord=3, MaxFragments=2, MinWords=5, MaxWords=12, HighlightAll=FALSE') AS snippet
                    FROM {{%static_content}} s, query
                    WHERE s.search_vector @@ query.q
                ) AS unioned
                ORDER BY rank DESC
                LIMIT 20
            ";

            $rows = $db->createCommand($sql, [':q' => $qNorm])->queryAll();

            // Normalize results and build URLs per type
            foreach ($rows as $row) {
                $type = (string)$row['type'];
                $title = (string)$row['title'];
                $slug = (string)$row['slug'];
                $rank = (float)$row['rank'];
                $snippet = (string)($row['snippet'] ?? '');

                if ($type === 'course') {
                    $url = Url::to(['course/view', 'slug' => $slug]);
                } elseif ($type === 'teacher') {
                    $url = Url::to(['teacher/view', 'slug' => $slug]);
                } elseif ($type === 'static') {
                    $url = Url::to(['site/' . $slug]);
                } else {
                    $url = '#';
                }

                $results[] = [
                    'type' => $type,
                    'title' => $title,
                    'url' => $url,
                    'snippet' => $snippet,
                    'rank' => $rank,
                ];
            }
        }

        return $this->renderPartial('_results', [
            'q' => $q,
            'results' => $results,
        ]);
    }
}
