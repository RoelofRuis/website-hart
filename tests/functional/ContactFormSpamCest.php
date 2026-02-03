<?php

namespace app\tests\functional;

use app\tests\FunctionalTester;
use app\models\ContactMessage;
use app\tests\fixtures\StaticContentFixture;

class ContactFormSpamCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'static' => StaticContentFixture::class,
        ]);
    }

    public function testSubmitSuccessfully(FunctionalTester $I)
    {
        $I->amOnPage('/contact');
        $I->submitForm('form[action*="/contact/submit"]', [
            'ContactMessage[name]' => 'Test User',
            'ContactMessage[email]' => 'test@example.com',
            'ContactMessage[message]' => 'This is a test message.',
            'ContactMessage[verify_email]' => '',
        ]);
        $I->see('Dank, je bericht is verstuurd!');
        $I->seeRecord(ContactMessage::class, ['email' => 'test@example.com']);
    }

    public function testSubmitWithHoneypotFails(FunctionalTester $I)
    {
        $I->amOnPage('/contact');
        $I->submitForm('form[action*="/contact/submit"]', [
            'ContactMessage[name]' => 'Spammer',
            'ContactMessage[email]' => 'spam@example.com',
            'ContactMessage[message]' => 'I am a bot.',
            'ContactMessage[verify_email]' => 'bot filling this field',
        ]);
        $I->dontSee('Dank, je bericht is verstuurd!');
        $I->dontSeeRecord(ContactMessage::class, ['email' => 'spam@example.com']);
    }
}
