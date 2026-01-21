<?php

namespace app\tests\functional;

use app\tests\fixtures\CourseFixture;
use app\tests\fixtures\CourseTagFixture;
use app\tests\fixtures\StaticContentFixture;
use app\tests\fixtures\StaticContentTagFixture;
use app\tests\fixtures\TagFixture;
use app\tests\fixtures\TeacherFixture;
use app\tests\fixtures\TeacherTagFixture;
use app\tests\fixtures\UserFixture;
use app\tests\FunctionalTester;

class SearchCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'users' => UserFixture::class,
            'teachers' => TeacherFixture::class,
            'courses' => CourseFixture::class,
            'static' => StaticContentFixture::class,
            'tags' => TagFixture::class,
            'teacher_tags' => TeacherTagFixture::class,
            'course_tags' => CourseTagFixture::class,
            'static_tags' => StaticContentTagFixture::class,
        ]);
    }

    public function testSearchByTag(FunctionalTester $I)
    {
        $I->amOnPage('/search?q=muziek');
        
        $I->see('Ferry Kuipers', '.card-title');
        $I->see('Ontdek de muziek', '.card-title');
        $I->see('Iris de Boer', '.card-title');
        $I->see('Muziektheorie', '.card-title');
    }

    public function testSearchByTeacherName(FunctionalTester $I)
    {
        $I->amOnPage('/search?q=Alice');
        $I->see('Alice van Dijk', '.card-title');
    }

    public function testSearchPartialMatch(FunctionalTester $I)
    {
        $I->amOnPage('/search?q=theo');
        $I->see('Ferry Kuipers', '.card-title');
        $I->see('Muziektheorie', '.card-title');
    }

    public function testNoResults(FunctionalTester $I)
    {
        $I->amOnPage('/search?q=nonexistentsearchterm');
        $I->see('Geen resultaten gevonden.', '.search-no-results');
        $I->see('Zoekopdracht wissen', 'button');
    }
}
