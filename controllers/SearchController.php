<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use app\models\CourseNode;

class SearchController extends Controller
{
    public function actionIndex(string $q = null)
    {
        $request = Yii::$app->request;
        $q = $q ?? $request->get('q');
        $type = (string)$request->get('type', 'all'); // all|courses|teachers|children
        $parentId = $request->get('parentId');
        $perPage = (int)$request->get('perPage', 12);
        if ($perPage <= 0) { $perPage = 12; }
        $page = (int)$request->get('page', 1);
        if ($page <= 0) { $page = 1; }
        $offset = ($page - 1) * $perPage;
        $suppressEmpty = (int)$request->get('suppressEmpty', 0) === 1;

        $results = [];
        $qNorm = trim((string)$q);
        $hasMore = false;
        if ($qNorm === '' || mb_strlen($qNorm) === 0) {
            // Default dataset without a query, filtered by type/parent when given
            if ($type === 'all') {
                // Union courses + teachers ordered by title
                $db = Yii::$app->db;
                $sql = "SELECT type, title, slug, image FROM (
                            SELECT 'course'::text AS type,
                                   c.name AS title,
                                   c.slug AS slug,
                                   c.cover_image AS image
                            FROM {{%course_node}} c
                            WHERE c.is_taught = TRUE
                          UNION ALL
                            SELECT 'teacher'::text AS type,
                                   t.full_name AS title,
                                   t.slug AS slug,
                                   t.profile_picture AS image
                            FROM {{%teacher}} t
                        ) AS unioned
                        ORDER BY title ASC
                        LIMIT :limit OFFSET :offset";
                $rows = $db->createCommand($sql, [
                    ':limit' => $perPage + 1,
                    ':offset' => $offset,
                ])->queryAll();
                if (count($rows) > $perPage) { $hasMore = true; array_pop($rows); }
                foreach ($rows as $row) {
                    $t = (string)$row['type'];
                    $slug = (string)$row['slug'];
                    $title = (string)$row['title'];
                    $image = isset($row['image']) ? (string)$row['image'] : null;
                    $url = $t === 'teacher'
                        ? Url::to(['teacher/view', 'slug' => $slug])
                        : Url::to(['course/view', 'slug' => $slug]);
                    $results[] = [
                        'type' => $t,
                        'title' => $title,
                        'url' => $url,
                        'snippet' => '',
                        'image' => $image,
                        'rank' => 0,
                    ];
                }
            } elseif ($type === 'teachers') {
                $teacherQuery = \app\models\Teacher::find()->orderBy(['full_name' => SORT_ASC]);
                $teacherRows = $teacherQuery->offset($offset)->limit($perPage + 1)->all();
                if (count($teacherRows) > $perPage) { $hasMore = true; array_pop($teacherRows); }
                foreach ($teacherRows as $t) {
                    $results[] = [
                        'type' => 'teacher',
                        'title' => (string)$t->full_name,
                        'url' => Url::to(['teacher/view', 'slug' => $t->slug]),
                        'snippet' => '',
                        'image' => $t->profile_picture ? (string)$t->profile_picture : null,
                        'rank' => 0,
                    ];
                }
            } else { // courses or all/children default to courses list
                $query = CourseNode::find();
                if ($type === 'children' && $parentId !== null) {
                    $query->where(['parent_id' => (int)$parentId]);
                } else {
                    $query->where(['is_taught' => true]);
                }
                $query->orderBy(['name' => SORT_ASC]);
                $rows = $query->offset($offset)->limit($perPage + 1)->all();
                if (count($rows) > $perPage) { $hasMore = true; array_pop($rows); }
                foreach ($rows as $course) {
                    $results[] = [
                        'type' => 'course',
                        'title' => (string)$course->name,
                        'url' => Url::to(['course/view', 'slug' => $course->slug]),
                        'snippet' => (string)($course->summary ?? ''),
                        'image' => $course->cover_image ? (string)$course->cover_image : null,
                        'rank' => 0,
                    ];
                }
            }
        } elseif ($qNorm !== '' && mb_strlen($qNorm) >= 2) {
            $db = Yii::$app->db;

            // Full-text search across course_node, teacher, and static_content using their search_vector columns.
            // Use websearch_to_tsquery for intuitive search terms, unaccent for diacritics-insensitive matching.
            $parts = [];
            $params = [':q' => $qNorm];
            if ($type === 'all' || $type === 'courses' || $type === 'children') {
                $courseWhere = "c.search_vector @@ query.q";
                if ($type === 'children' && $parentId !== null) {
                    $courseWhere .= " AND c.parent_id = :parentId";
                    $params[':parentId'] = (int)$parentId;
                }
                $parts[] = "SELECT 'course'::text AS type,
                                   c.name AS title,
                                   c.slug AS slug,
                                   ts_rank_cd(c.search_vector, query.q, 32) AS rank,
                                   ts_headline('dutch', COALESCE(c.summary, ''), query.q,
                                               'ShortWord=3, MaxFragments=2, MinWords=5, MaxWords=12, HighlightAll=FALSE') AS snippet,
                                   c.cover_image AS image
                            FROM {{%course_node}} c, query
                            WHERE $courseWhere";
            }
            if ($type === 'all' || $type === 'teachers') {
                $parts[] = "SELECT 'teacher'::text AS type,
                                   t.full_name AS title,
                                   t.slug AS slug,
                                   ts_rank_cd(t.search_vector, query.q, 32) AS rank,
                                   ts_headline('dutch', COALESCE(t.description, ''), query.q,
                                               'ShortWord=3, MaxFragments=2, MinWords=5, MaxWords=12, HighlightAll=FALSE') AS snippet,
                                   t.profile_picture AS image
                            FROM {{%teacher}} t, query
                            WHERE t.search_vector @@ query.q";
            }
            if ($type === 'all') {
                $parts[] = "SELECT 'static'::text AS type,
                                   s.key AS title,
                                   s.slug AS slug,
                                   ts_rank_cd(s.search_vector, query.q, 32) AS rank,
                                   ts_headline('dutch', COALESCE(s.content, ''), query.q,
                                               'ShortWord=3, MaxFragments=2, MinWords=5, MaxWords=12, HighlightAll=FALSE') AS snippet,
                                   NULL::text AS image
                            FROM {{%static_content}} s, query
                            WHERE s.search_vector @@ query.q";
            }

            $unionSql = implode("\nUNION ALL\n", $parts);
            $sql = "WITH query AS (
                        SELECT websearch_to_tsquery('dutch', unaccent(:q)) AS q
                    )
                    SELECT type, title, slug, rank, snippet, image
                    FROM ( $unionSql ) AS unioned
                    ORDER BY rank DESC
                    LIMIT :limit OFFSET :offset";

            $params[':limit'] = $perPage + 1;
            $params[':offset'] = $offset;
            $rows = $db->createCommand($sql, $params)->queryAll();

            // Normalize results and build URLs per type
            if (count($rows) > $perPage) { $hasMore = true; array_pop($rows); }
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
                    $url = Url::to(['static-page/view', 'slug' => $slug]);
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
        }

        return $this->renderPartial('_results', [
            'q' => $q,
            'results' => $results,
            'hasMore' => $hasMore,
            'nextPage' => $hasMore ? $page + 1 : null,
            'suppressEmpty' => $suppressEmpty,
        ]);
    }
}
