<?php

namespace app\tests\functional;

use app\tests\fixtures\CourseFixture;
use app\tests\fixtures\TeacherFixture;
use app\tests\fixtures\UserFixture;
use app\tests\FunctionalTester;

class CourseFormCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'users' => UserFixture::class,
            'teachers' => TeacherFixture::class,
            'courses' => CourseFixture::class,
        ]);
    }

    public function testTeacherDropdown(FunctionalTester $I)
    {
        $I->amLoggedInAs(1); // Alice van Dijk (Admin)
        $I->amOnPage('/course/create');
        $I->see('Cursus toevoegen', 'h1');

        $I->see('Alice van Dijk');
        $I->see('Bob Jansen');
        $I->see('Carla de Vries');
    }
}
