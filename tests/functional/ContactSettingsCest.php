<?php

namespace app\tests\functional;

use app\models\ContactMessage;
use app\models\ContactTypeReceiver;
use app\tests\fixtures\ContactMessageFixture;
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
        $I->checkOption('ContactTypeReceiver[contact_page][]', '2');
        $I->checkOption('ContactTypeReceiver[contact_page][]', '3');
        $I->click('Instellingen opslaan');

        $I->see('Instellingen succesvol opgeslagen.');
        $I->seeCheckboxIsChecked('ContactTypeReceiver[contact_page][]', '2');
        $I->seeCheckboxIsChecked('ContactTypeReceiver[contact_page][]', '3');

        // Verify in DB
        $I->seeRecord(ContactTypeReceiver::class, ['type' => 'contact_page', 'user_id' => 2]);
        $I->seeRecord(ContactTypeReceiver::class, ['type' => 'contact_page', 'user_id' => 3]);

        // Now test routing: submit a general contact message
        $I->amOnPage('/contact');
        $I->submitForm('form', [
            'ContactMessage[name]' => 'New Sender',
            'ContactMessage[email]' => 'new@example.com',
            'ContactMessage[message]' => 'Test message for routing',
            'ContactMessage[type]' => 'teacher_contact',
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
