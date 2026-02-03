<?php

namespace app\tests\functional;

use app\tests\fixtures\ContactMessageFixture;
use app\tests\fixtures\ContactMessageUserFixture;
use app\tests\fixtures\UserFixture;
use app\tests\FunctionalTester;

class ContactStatsCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'users' => UserFixture::class,
            'messages' => ContactMessageFixture::class,
            'message_users' => ContactMessageUserFixture::class,
        ]);
    }

    public function testAdminAccessStats(FunctionalTester $I)
    {
        $I->amLoggedInAs(1); // Admin
        $I->amOnPage('/portaal/alle-berichten');
        $I->seeLink('Statistieken');
        $I->click('Statistieken');
        
        $I->seeInCurrentUrl('/portaal/bericht-statistieken');
        $I->see('Berichtstatistieken', 'h1');
        $I->seeElement('#statsChart');
        $I->dontSee('Filter op type', 'label');
    }

    public function testNonAdminDeniedStats(FunctionalTester $I)
    {
        $I->amLoggedInAs(2); // Non-Admin
        $I->amOnPage('/portaal/bericht-statistieken');
        $I->seeResponseCodeIs(403);
    }
}
