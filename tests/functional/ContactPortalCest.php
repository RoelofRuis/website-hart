<?php

namespace app\tests\functional;

use app\tests\fixtures\ContactMessageFixture;
use app\tests\fixtures\ContactMessageUserFixture;
use app\tests\fixtures\StaticContentFixture;
use app\tests\fixtures\UrlRuleFixture;
use app\tests\fixtures\UserFixture;
use app\tests\FunctionalTester;

class ContactPortalCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'users' => UserFixture::class,
            'messages' => ContactMessageFixture::class,
            'message_users' => ContactMessageUserFixture::class,
            'url_rules' => UrlRuleFixture::class,
            'static' => StaticContentFixture::class,
        ]);
    }

    public function testTeacherMessages(FunctionalTester $I)
    {
        $I->amLoggedInAs(1); // Alice (Admin)
        $I->amOnPage('/contact/messages');
        $I->see('Jouw berichten', 'h1');
        
        // See her messages
        $I->see('John Doe');
        $I->see('Alice Wonder');
        // Should NOT see Jane's message (she is assigned to user 2)
        $I->dontSee('Jane Smith');
        
        // Test search
        $I->fillField('ContactMessageSearch[q]', 'Wonder');
        $I->click('search-button');
        $I->see('Alice Wonder');
        $I->dontSee('John Doe');
        
        // Reset search to see all for sorting test
        $I->fillField('ContactMessageSearch[q]', '');
        $I->click('search-button');

        // Test sorting (created_at is default DESC)
        // message 4 (Alice Wonder) 2026-01-11 14:00:00
        // message 1 (John Doe) 2026-01-10 10:00:00
        $I->seeElement('//table/tbody/tr[1]/td[contains(., "Alice Wonder")]');
        
        // Click the sort link for 'Sent on'
        $I->click('Verzonden op');
        // Now it should be ASC, so John Doe (Jan 10) comes first.
        $I->seeElement('//table/tbody/tr[1]/td[contains(., "John Doe")]');
    }

    public function testAdminAllMessages(FunctionalTester $I)
    {
        $I->amLoggedInAs(1); // Admin
        $I->amOnPage('/contact/messages');
        $I->seeLink('Toon alle berichten');
        $I->click('Toon alle berichten');
        
        $I->seeInCurrentUrl('/portaal/alle-berichten');
        $I->see('Alle berichten', 'h1');
        
        // Should see all messages
        $I->see('John Doe');
        $I->see('Jane Smith');
        $I->see('Bob Brown');
        $I->see('Alice Wonder');
        
        // Check highlighting for message 3 (no receiver)
        $I->seeElement('//table/tbody/tr[contains(@class, "table-danger")]/td[contains(., "Bob Brown")]');
        
        // Test update receivers for Bob Brown (id 3)
        // Alice is user 1.
        $I->checkOption('//tr[td[contains(., "Bob Brown")]]//input[@name="receivers[]"][@value="1"]');
        $I->click('#update-btn-3');
        
        $I->seeInCurrentUrl('/portaal/alle-berichten');
        // Row should no longer be highlighted
        $I->dontSeeElement('//table/tbody/tr[contains(@class, "table-danger")]/td[contains(., "Bob Brown")]');
        // Bob Brown should now have Alice as receiver
        $I->see('Alice van Dijk', '//tr[td[contains(., "Bob Brown")]]');
    }

    public function testNonAdminAccess(FunctionalTester $I)
    {
        $I->amLoggedInAs(2); // Bob (Non-Admin)
        $I->amOnPage('/contact/messages');
        $I->dontSeeLink('Show all messages');
        
        $I->amOnPage('/contact/all-messages');
        $I->seeResponseCodeIs(403);
    }
}
