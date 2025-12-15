<?php

namespace app\widgets;

use app\models\CourseNode;
use yii\base\Widget;

class CourseSelectionWidget extends Widget
{
    public function run(): string
    {
        $courses = CourseNode::findTaughtCourses()->limit(10)->all();

        if (empty($courses)) {
            return '';
        }

        return $this->render('course-selection', [
            'courses' => $courses,
        ]);
    }
}
