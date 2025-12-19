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
        foreach (StaticContent::find()->all() as $content) {
            $content->save();
        }

        foreach (Teacher::find()->all() as $teacher) {
            $teacher->save();
        }

        foreach (CourseNode::find()->all() as $node) {
            $node->save();
        }
    }
}