<?php
namespace app\tests\functional;

use app\tests\fixtures\StaticContentFixture;
use app\tests\FunctionalTester;

class StaticContentCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'static' => StaticContentFixture::class,
        ]);
    }

    public function testLastUpdatedVisible(FunctionalTester $I)
    {
        $I->amOnPage('/over-vhm');
        $I->see('Laatst bijgewerkt:');
        
        $I->amOnPage('/avg-privacy');
        $I->see('Laatst bijgewerkt:');

        $I->amOnPage('/contact');
        $I->see('Laatst bijgewerkt:');
    }
}
