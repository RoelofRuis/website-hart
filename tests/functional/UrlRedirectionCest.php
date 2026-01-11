<?php

namespace app\tests\functional;

use app\models\UrlRule;
use app\tests\FunctionalTester;

class UrlRedirectionCest
{
    public function testPermanentRedirect(FunctionalTester $I)
    {
        // 1. Create a URL rule
        $sourceUrl = '/old-page';
        $targetUrl = '/docenten';
        
        $rule = new UrlRule();
        $rule->source_url = $sourceUrl;
        $rule->target_url = $targetUrl;
        $rule->save();

        // 2. Access the old URL
        // Note: Codeception's amOnPage might follow redirects automatically.
        // We can check the final destination.
        $I->amOnPage($sourceUrl);
        $I->seeCurrentUrlEquals($targetUrl);

        // 3. Verify hit counter
        $rule->refresh();
        $I->assertEquals(1, $rule->hit_counter);
    }

    public function testRedirectWithDifferentPathFormats(FunctionalTester $I)
    {
        // Test matching without leading slash in DB if requested with it
        $rule = new UrlRule();
        $rule->source_url = 'another-old-page';
        $rule->target_url = '/cursussen';
        $rule->save();

        $I->amOnPage('/another-old-page');
        $I->seeCurrentUrlEquals('/cursussen');
        
        $rule->refresh();
        $I->assertEquals(1, $rule->hit_counter);
    }
}
