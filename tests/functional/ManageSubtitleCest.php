<?php
namespace app\tests\functional;

use app\tests\fixtures\UserFixture;
use app\tests\FunctionalTester;

class ManageSubtitleCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'user' => UserFixture::class,
        ]);
    }

    public function testSubtitleVisible(FunctionalTester $I)
    {
        // Login as Alice (id: 1)
        $I->amOnPage('/site/login');
        $I->fillField('LoginForm[email]', 'alice@example.com');
        $I->fillField('LoginForm[password]', 'password');
        $I->click('button[type="submit"]');
        
        $I->see('Docentenportaal', 'h1');
        $I->see('welkom Alice van Dijk');
    }
}
