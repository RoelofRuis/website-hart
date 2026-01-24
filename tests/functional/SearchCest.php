<?php

namespace app\tests\functional;

use app\tests\fixtures\CourseFixture;
use app\tests\fixtures\CourseTeacherFixture;
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
            'course_teachers' => CourseTeacherFixture::class,
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

        // Verify icons are present
        $I->seeElement('.bi-person'); // Teacher
        $I->seeElement('.bi-book');   // Course
        // No static content matches 'muziek' in fixtures, so we check it in a separate page
        
        $I->amOnPage('/search?q=contact');
        $I->seeElement('.bi-info-circle'); // Static content (Contact page)
    }

    public function testSearchByTeacherName(FunctionalTester $I)
    {
        $I->amOnPage('/search?q=Alice');
        $I->see('Alice van Dijk', '.card-title');
    }

    public function testSearchByTeacherNameNoTag(FunctionalTester $I)
    {
        // 'Bob Jansen' is teacher with id 2.
        // He has tags: Gitaar (id 4), Jazz (id 5), Pop (id 6)
        // I will search for 'Jansen' which is not a tag but part of his full name.
        $I->amOnPage('/search?q=Jansen');
        $I->see('Bob Jansen', '.card-title');
    }

    public function testSearchByCourseName(FunctionalTester $I)
    {
        $I->amOnPage('/search?q=Harp');
        $I->see('Harp', '.card-title');
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
