<?php

namespace app\tests\functional;

use app\models\ContactMessage;
use app\models\ContactTypeReceiver;
use app\tests\fixtures\ContactMessageFixture;
use app\tests\fixtures\StaticContentFixture;
use app\tests\fixtures\TeacherFixture;
use app\tests\fixtures\UserFixture;
use app\tests\FunctionalTester;

class ContactSettingsCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'users' => UserFixture::class,
            'teachers' => TeacherFixture::class,
            'messages' => ContactMessageFixture::class,
            'static' => StaticContentFixture::class,
        ]);
    }

    public function testAdminCanManageSettings(FunctionalTester $I)
    {
        $I->amLoggedInAs(1); // Alice (Admin)
        $I->amOnPage('/site/manage');
        $I->see('Contactinstellingen', 'h5');
        $I->click('Contactinstellingen');

        $I->seeInCurrentUrl('/portaal/contact');
        $I->see('Contactinstellingen', 'h1');
        $I->see('Contactpagina');

        // Select Bob (id 2) and Carla (id 3) as receivers for contact page
        $I->checkOption('#chk-general_contact-2');
        $I->checkOption('#chk-general_contact-3');
        $I->click('Opslaan');

        $I->see(' Instellingen opgeslagen');
        $I->seeCheckboxIsChecked('#chk-general_contact-2');
        $I->seeCheckboxIsChecked('#chk-general_contact-3');

        $I->seeRecord(ContactTypeReceiver::class, ['type' => 'general_contact', 'user_id' => 2]);
        $I->seeRecord(ContactTypeReceiver::class, ['type' => 'general_contact', 'user_id' => 3]);

        // Now test routing: submit a general contact message
        $I->amOnPage('/contact');
        $I->submitForm('form', [
            'ContactMessage[name]' => 'New Sender',
            'ContactMessage[email]' => 'new@example.com',
            'ContactMessage[message]' => 'Test message for routing',
        ]);

        $I->see('Dank, je bericht is verstuurd!');

        // Check if message is assigned to Bob and Carla
        /** @var ContactMessage $message */
        $message = $I->grabRecord(ContactMessage::class, ['email' => 'new@example.com']);
        $receiverIds = $message->getUsers()->select('id')->column();
        $I->assertContains(2, $receiverIds);
        $I->assertContains(3, $receiverIds);
        $I->assertCount(2, $receiverIds);
    }

    public function testNonAdminCannotAccessSettings(FunctionalTester $I)
    {
        $I->amLoggedInAs(2); // Bob (Non-Admin)
        $I->amOnPage('/contact/settings');
        $I->seeResponseCodeIs(403);
    }
}
