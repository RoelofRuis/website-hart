<?php
namespace app\tests\functional;

use app\models\ContactMessage;
use app\tests\fixtures\ContactMessageFixture;
use app\tests\fixtures\StaticContentFixture;
use app\tests\fixtures\TeacherFixture;
use app\tests\FunctionalTester;

class ContactSubmitCest
{
    private function validData(array $override = []): array
    {
        return array_merge([
            'ContactMessage[name]' => 'Jan Jansen',
            'ContactMessage[email]' => 'jan@example.com',
            'ContactMessage[message]' => 'Hallo! Dit is een testbericht.',
            'ContactMessage[type]' => ContactMessage::TYPE_CONTACT,
        ], $override);
    }

    public function submit_general_contact_success(FunctionalTester $I): void
    {
        $I->haveFixtures([
            'contacts' => ContactMessageFixture::class,
            'static' => StaticContentFixture::class,
        ]);

        // Open the contact page so the referrer is set and flash is rendered on redirect
        $I->amOnPage('/contact');

        $I->submitForm('form', $this->validData());

        // After submit we should be back on the contact page
        $I->seeCurrentUrlEquals('/contact');

        // Flash message should be visible
        $I->see('Dank, je bericht is verstuurd!');

        // Data should be stored in DB
        $I->seeRecord(ContactMessage::class, [
            'email' => 'jan@example.com',
            'name' => 'Jan Jansen',
            'type' => ContactMessage::TYPE_CONTACT,
        ]);
    }

    public function submit_teacher_contact_success(FunctionalTester $I): void
    {
        $I->haveFixtures([
            'contacts' => ContactMessageFixture::class,
            'teachers' => TeacherFixture::class,
        ]);

        $I->amOnPage('/docent/alice-van-dijk');

        $I->submitForm('form', $this->validData([
            'ContactMessage[type]' => ContactMessage::TYPE_CONTACT,
            'ContactMessage[teacher_id]' => 1,
            'ContactMessage[email]' => 'bob@example.com',
        ]));

        $I->seeCurrentUrlEquals('/docent/alice-van-dijk');
        $I->see('Dank, je bericht is verstuurd!');


        /** @var ContactMessage $record */
        $record = $I->grabRecord(ContactMessage::class, ['id' => 1]);
        $I->assertSame('bob@example.com', $record->email);
        $I->assertSame('Jan Jansen', $record->name);
        $I->assertSame('Hallo! Dit is een testbericht.', $record->message);
        $I->assertSame(ContactMessage::TYPE_CONTACT, $record->type);
        $I->assertSame([1], $record->getTeachers()->column());
    }

    public function submit_missing_fields_error(FunctionalTester $I): void
    {
        $I->haveFixtures([
            'contacts' => ContactMessageFixture::class,
            'static' => StaticContentFixture::class,
        ]);

        $I->amOnPage('/contact');
        $I->submitForm('form', $this->validData([
            'ContactMessage[name]' => '',
        ]));

        $I->seeCurrentUrlEquals('/contact');
        $I->see('Corrigeer de fouten in het formulier.');

        // Ensure nothing was stored
        $I->dontSeeRecord(ContactMessage::class, [
            'email' => 'jan@example.com',
        ]);
    }

    public function submit_invalid_email_error(FunctionalTester $I): void
    {
        $I->haveFixtures([
            'contacts' => ContactMessageFixture::class,
            'static' => StaticContentFixture::class,
        ]);

        $I->amOnPage('/contact');
        $I->submitForm('form', $this->validData([
            'ContactMessage[email]' => 'not-an-email',
        ]));

        $I->seeCurrentUrlEquals('/contact');
        $I->see('Corrigeer de fouten in het formulier.');
        $I->dontSeeRecord(ContactMessage::class, [
            'email' => 'not-an-email',
        ]);
    }

    public function submit_invalid_type_error(FunctionalTester $I): void
    {
        $I->haveFixtures([
            'contacts' => ContactMessageFixture::class,
            'static' => StaticContentFixture::class,
        ]);

        $I->amOnPage('/contact');
        $I->submitForm('form', $this->validData([
            'ContactMessage[type]' => 'invalid_type_value',
        ]));

        $I->seeCurrentUrlEquals('/contact');
        $I->see('Corrigeer de fouten in het formulier.');
        $I->dontSeeRecord(ContactMessage::class, [
            'email' => 'jan@example.com',
            'type' => 'invalid_type_value',
        ]);
    }
}
