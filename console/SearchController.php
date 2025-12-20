<?php

namespace app\console;

use app\models\CourseNode;
use app\models\StaticContent;
use app\models\Teacher;
use yii\console\Controller;

/**
 * Manage the searchable content.
 */
class SearchController extends Controller
{
    /**
     * Forces a reindex of all searchable content.
     */
    public function actionReindex()
    {
        $total = 0;
        foreach (StaticContent::find()->all() as $content) {
            $content->save();
            $total++;
        }

        foreach (Teacher::find()->all() as $teacher) {
            $teacher->save();
            $total++;
        }

        foreach (CourseNode::find()->all() as $node) {
            $node->save();
            $total++;
        }

        echo "Reindexed $total items.\n";
    }
}