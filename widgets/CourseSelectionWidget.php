<?php

namespace app\widgets;

use app\models\CourseNode;
use Yii;
use yii\base\Widget;

/**
 * Displays a selection of available courses on the homepage.
 */
class CourseSelectionWidget extends Widget
{
    /** @var int Number of courses to show */
    public int $limit = 6;

    /** @var string Optional heading displayed above the grid */
    public string $heading = '';

    public function run(): string
    {
        $query = CourseNode::find() // TODO: move query to CourseNode
            ->alias('c')
            ->where(['c.is_taught' => true])
            ->innerJoinWith('lessonFormats lf', false)
            ->groupBy('c.id')
            ->orderBy(['c.name' => SORT_ASC])
            ->limit($this->limit);

        $courses = $query->all();

        if (empty($courses)) {
            return '';
        }

        $heading = $this->heading !== ''
            ? $this->heading
            : Yii::t('app', 'Available Courses');

        return $this->render('course-selection', [
            'courses' => $courses,
            'heading' => $heading,
        ]);
    }
}
