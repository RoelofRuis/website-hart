<?php

namespace app\widgets;

use app\models\CourseNode;
use Yii;
use yii\base\Widget;
use yii\bootstrap5\Html;
use yii\helpers\Url;

/**
 * Displays a selection of available courses on the homepage.
 *
 * Available courses are those with is_taught = true and having at least one
 * attached LessonFormat.
 */
class CourseSelectionWidget extends Widget
{
    /** @var int Number of courses to show */
    public int $limit = 6;

    /** @var string Optional heading displayed above the grid */
    public string $heading = '';

    public function run(): string
    {
        $query = CourseNode::find()
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

        $html = [];

        $heading = $this->heading !== ''
            ? $this->heading
            : Yii::t('app', 'Available Courses');

        $html[] = Html::tag('h2', Html::encode($heading), ['class' => 'mb-4 text-center']);

        $html[] = Html::beginTag('div', ['class' => 'row']);
        foreach ($courses as $course) {
            /** @var CourseNode $course */
            $html[] = Html::beginTag('div', ['class' => 'col-md-4 mb-4']);

            $card = [];
            $card[] = Html::beginTag('div', ['class' => 'card h-100 shadow-sm']);

            // Cover image (optional)
            if (!empty($course->cover_image)) {
                $card[] = Html::img($course->cover_image, [
                    'class' => 'card-img-top',
                    'alt' => Html::encode($course->name),
                ]);
            }

            $card[] = Html::beginTag('div', ['class' => 'card-body d-flex flex-column']);
            $card[] = Html::tag('h5', Html::encode($course->name), ['class' => 'card-title']);
            if (!empty($course->summary)) {
                $card[] = Html::tag('p', Html::encode($course->summary), ['class' => 'card-text text-muted']);
            }

            $card[] = Html::tag('div',
                Html::a(Yii::t('app', 'View course'), ['course/view', 'slug' => $course->slug], [
                    'class' => 'btn btn-primary mt-auto',
                ]),
                ['class' => 'mt-auto']
            );

            $card[] = Html::endTag('div'); // card-body
            $card[] = Html::endTag('div'); // card

            $html[] = implode("\n", $card);
            $html[] = Html::endTag('div'); // col
        }
        $html[] = Html::endTag('div'); // row

        // Link to see all courses
        $html[] = Html::tag('div',
            Html::a(Yii::t('app', 'Explore all courses'), Url::to(['course/index']), [
                'class' => 'btn btn-outline-secondary'
            ]),
            ['class' => 'text-center mt-3']
        );

        return implode("\n", $html);
    }
}
