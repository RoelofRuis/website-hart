<?php

namespace app\tests\functional;

use app\models\Course;
use app\models\Teacher;
use app\models\User;
use app\tests\fixtures\CourseFixture;
use app\tests\fixtures\TeacherFixture;
use app\tests\fixtures\UserFixture;
use app\tests\FunctionalTester;

class TagAutomaticUpdateCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'users' => UserFixture::class,
            'teachers' => TeacherFixture::class,
            'courses' => CourseFixture::class,
        ]);
    }

    public function testCourseTagAutomaticUpdate(FunctionalTester $I)
    {
        $I->amLoggedInAs(1); // Admin
        $I->amOnPage('/course/update?id=1');
        $I->see('Cursus aanpassen', 'h1');
        
        $I->fillField('Course[name]', 'Initial Name');
        $I->click('Opslaan');
        
        $course = Course::findOne(1);
        $tags = array_map('trim', explode(',', $course->tags));
        $I->assertContains('Initial Name', $tags);

        $I->amOnPage('/course/update?id=1');
        $I->fillField('Course[name]', 'Updated Course Name');
        $I->fillField('Course[tags]', 'tag1, tag2');
        $I->click('Opslaan');
        
        $I->see('Cursus succesvol aangepast.');
        
        $course->refresh();
        $tags = array_map('trim', explode(',', $course->tags));
        $I->assertContains('Updated Course Name', $tags);
        $I->assertNotContains('Initial Name', $tags);
        $I->assertContains('tag1', $tags);
        $I->assertContains('tag2', $tags);
    }

    public function testTeacherTagAutomaticUpdate(FunctionalTester $I)
    {
        // Teacher 1 is Alice van Dijk (user_id 1)
        $I->amLoggedInAs(1); 
        
        $I->amOnPage('/user/update?id=1');
        $I->fillField('User[full_name]', 'Alice Updated');
        $I->fillField('Teacher[tags]', 'expert, beginner');
        $I->click('Wijzigingen opslaan');
        
        $I->see('Profiel succesvol bijgewerkt.');
        
        $teacher = Teacher::findOne(['user_id' => 1]);
        $tags = array_map('trim', explode(',', $teacher->tags));
        $I->assertContains('Alice Updated', $tags);
        $I->assertNotContains('Alice van Dijk', $tags);
        $I->assertContains('expert', $tags);
        $I->assertContains('beginner', $tags);
    }
}
