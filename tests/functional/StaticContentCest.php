<?php
namespace app\tests\functional;

use app\tests\fixtures\StaticContentFixture;
use app\tests\FunctionalTester;
use Codeception\Example;

class StaticContentCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'static' => StaticContentFixture::class,
        ]);
    }

    /** @dataProvider _timestampedPagesProvider */
    public function testLastUpdatedVisible(FunctionalTester $I, Example $example)
    {
        $I->amOnPage($example[0]);
        $I->see('Laatst bijgewerkt:');
    }

    public function _timestampedPagesProvider()
    {
        return [
            ['/over-vhm'],
            ['/vereniging'],
            ['/contact'],
            ['/avg-privacy'],
            ['/auteursrecht'],
            ['/instrumentenverhuur'],
            ['/jeugdfonds'],
        ];
    }
}
