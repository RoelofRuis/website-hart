<?php

namespace app\tests\functional;

use app\tests\fixtures\LocationFixture;
use app\tests\fixtures\TeacherFixture;
use app\tests\fixtures\TeacherLocationFixture;
use app\tests\fixtures\UserFixture;
use app\tests\FunctionalTester;

class TeacherUpdateCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'users' => UserFixture::class,
            'teachers' => TeacherFixture::class,
            'locations' => LocationFixture::class,
            'teacher_locations' => TeacherLocationFixture::class,
        ]);
    }

    public function testTeacherCanUpdateLocations(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->fillField('LoginForm[email]', 'alice@example.com');
        $I->fillField('LoginForm[password]', 'password');
        $I->click('button[type="submit"]');

        $I->amOnPage('/user/update?id=1');
        $I->see('Profiel bijwerken', 'h1');

        $I->uncheckOption('input[name="Teacher[location_ids][]"][value="1"]');
        $I->checkOption('input[name="Teacher[location_ids][]"][value="3"]');

        $I->click('Wijzigingen opslaan');

        $I->see('Profiel succesvol bijgewerkt.');

        $I->amOnPage('/docent/alice-van-dijk');
        $I->dontSee('Hoofdgebouw', '.teacher-info a');
        $I->see('Slachthuis', '.teacher-info a');
        $I->seeElement('.teacher-info a', ['href' => '/locaties#location-3']);
    }
}
