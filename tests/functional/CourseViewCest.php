<?php

namespace app\tests\functional;

use app\tests\fixtures\CourseFixture;
use app\tests\fixtures\CourseTeacherFixture;
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
        $I->see('Docenten voor deze cursus', 'h3');
        $I->see('Gina Vos', '.card-title');
        $I->see('Joris Willems', '.card-title');
        $I->see('Alice van Dijk', '.card-title');
    }

    public function testNoTeachersMessage(FunctionalTester $I)
    {
        // Course ID 5 is "Cello" - no teachers in course_teacher.php for ID 5
        $I->amOnPage('/cursus/cello');
        $I->see('Er zijn nog geen docenten voor deze cursus.');
    }
}
