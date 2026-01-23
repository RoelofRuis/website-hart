<?php

namespace app\tests\functional;

use app\tests\fixtures\CourseFixture;
use app\tests\fixtures\CourseTeacherFixture;
use app\tests\fixtures\LessonFormatFixture;
use app\tests\fixtures\TeacherFixture;
use app\tests\fixtures\UserFixture;
use app\tests\FunctionalTester;

class CourseViewCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'users' => UserFixture::class,
            'teachers' => TeacherFixture::class,
            'courses' => CourseFixture::class,
            'course_teachers' => CourseTeacherFixture::class,
            'lesson_formats' => LessonFormatFixture::class,
        ]);
    }

    public function testSeeTeachersOnCoursePage(FunctionalTester $I)
    {
        // Course ID 2 is "Piano"
        // Teachers for Piano (course_id 2):
        // - teacher_id 7 (Gina Vos)
        // - teacher_id 10 (Joris Willems)
        // - teacher_id 1 (Alice van Dijk)

        $I->amOnPage('/cursus/piano');
        $I->see('Piano', 'h1');
        $I->see('Docenten voor deze cursus', 'h2');
        $I->see('Gina Vos', '.card-title');
        $I->see('Joris Willems', '.card-title');
        $I->see('Alice van Dijk', '.card-title');

        // Check if taught courses are shown instead of summary
        // Alice van Dijk (teacher 1) teaches Piano (course 2)
        $I->see('Piano', '.card-text');
    }

    public function testCourseWithoutTeachersReturns404(FunctionalTester $I)
    {
        $I->amOnPage('/cursus/cello');
        $I->seeResponseCodeIs(404);
    }
}
