<?php

namespace app\tests\functional;

use app\models\User;
use app\tests\fixtures\CourseFixture;
use app\tests\fixtures\CourseTeacherFixture;
use app\tests\fixtures\TeacherFixture;
use app\tests\fixtures\UserFixture;
use app\tests\FunctionalTester;

class VisibilityCest
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

    public function testVisibility(FunctionalTester $I)
    {
        // Initially Alice is visible
        $I->amOnPage('/search?type=teachers');
        $I->see('Alice van Dijk');
        $I->amOnPage('/docent/alice-van-dijk');
        $I->seeResponseCodeIs(200);
        $I->see('Alice van Dijk');

        // Initially Course "Piano" is visible (it has Alice, Gina, Joris)
        $I->amOnPage('/search?type=courses');
        $I->see('Piano');
        $I->amOnPage('/cursus/piano');
        $I->seeResponseCodeIs(200);
        $I->see('Piano');

        // Hide Alice
        $alice = User::findOne(['full_name' => 'Alice van Dijk']);
        $alice->is_visible = false;
        $alice->save();

        // Alice should NOT be in search results
        $I->amOnPage('/search?type=teachers');
        $I->dontSee('Alice van Dijk', '.card-title');

        // Alice profile should NOT be accessible
        $I->amOnPage('/docent/alice-van-dijk');
        $I->seeResponseCodeIs(404);

        // Course "Piano" should still be visible because Gina and Joris are still visible
        $I->amOnPage('/search?type=courses');
        $I->see('Piano');
        $I->amOnPage('/cursus/piano');
        $I->seeResponseCodeIs(200);

        // Alice should NOT be in the teacher list results anymore
        $I->dontSee('Alice van Dijk', '.card-title');

        // Hide Gina and Joris
        $gina = User::findOne(['full_name' => 'Gina Vos']);
        $gina->is_visible = false;
        $gina->save();

        $joris = User::findOne(['full_name' => 'Joris Willems']);
        $joris->is_visible = false;
        $joris->save();

        // Now Course "Piano" should NOT be visible in search
        $I->amOnPage('/search?type=courses');
        $I->dontSee('Piano');

        // Course "Piano" profile should NOT be accessible
        $I->amOnPage('/cursus/piano');
        $I->seeResponseCodeIs(404);
    }
}
