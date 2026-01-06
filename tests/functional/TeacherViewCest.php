<?php

namespace app\tests\functional;

use app\tests\fixtures\LocationFixture;
use app\tests\fixtures\TeacherFixture;
use app\tests\fixtures\TeacherLocationFixture;
use app\tests\fixtures\UserFixture;
use app\tests\FunctionalTester;

class TeacherViewCest
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

    public function testTeacherViewShowsDaysAndLocations(FunctionalTester $I)
    {
        $I->amOnPage('/docent/alice-van-dijk');
        
        // Check for days (Dutch since language is nl-NL in config/test.php)
        $I->see('Maandag', '.teacher-info');
        $I->see('Dinsdag', '.teacher-info');
        
        // Check for locations
        $I->see('Hoofdgebouw', '.teacher-info');
        $I->see('Slachthuis', '.teacher-info');
    }
}
