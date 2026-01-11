<?php
namespace app\tests\functional;

use app\models\ContactMessage;
use app\tests\fixtures\ContactMessageFixture;
use app\tests\fixtures\StaticContentFixture;
use app\tests\fixtures\TeacherFixture;
use app\tests\FunctionalTester;
use Codeception\Example;

class ContactSubmitCest
{
    private function validData(array $override = []): array
    {
        return array_merge([
            'ContactMessage[name]' => 'Jan Jansen',
            'ContactMessage[email]' => 'jan@example.com',
            'ContactMessage[message]' => 'Hallo! Dit is een testbericht.',
            'ContactMessage[type]' => ContactMessage::TYPE_TEACHER_CONTACT,
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
            'type' => ContactMessage::TYPE_TEACHER_CONTACT,
        ]);
    }

    /** @dataProvider _teacherContactProvider */
    public function submit_teacher_contact_success(FunctionalTester $I, Example $example): void
    {
        $I->haveFixtures([
            'contacts' => ['class' => ContactMessageFixture::class, 'dataFile' => false],
            'teachers' => TeacherFixture::class,
        ]);

        $I->amOnPage('/docent/alice-van-dijk');

        $I->submitForm('form', $this->validData([
            'ContactMessage[name]' => $example['name'],
            'ContactMessage[type]' => $example['type'],
            'ContactMessage[user_id]' => $example['user_id'],
            'ContactMessage[email]' => $example['email'],
            'ContactMessage[message]' => $example['message'] ?? null,
        ]));

        $I->seeCurrentUrlEquals('/docent/alice-van-dijk');
        $I->see('Dank, je bericht is verstuurd!');

        /** @var ContactMessage $record */
        $record = $I->grabRecord(ContactMessage::class, ['id' => 1]);
        $I->assertSame($example['name'], $record->name);
        $I->assertSame($example['email'], $record->email);
        $I->assertSame($example['message'] ?? '', $record->message);
        $I->assertSame($example['type'], $record->type);
        $I->assertSame([$example['user_id']], $record->getUsers()->column());
    }

    public function _teacherContactProvider(): array
    {
        return [
            'contact' => [
                'name' => 'Bob',
                'type' => 'teacher_contact',
                'user_id' => 1,
                'email' => 'bob@example.com',
                'message' => 'Hallo! Dit is een testbericht.',
            ],
            'plan' => [
                'name' => 'Carly',
                'type' => 'teacher_plan',
                'user_id' => 1,
                'email' => 'carly@example.com',
            ],
        ];
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
