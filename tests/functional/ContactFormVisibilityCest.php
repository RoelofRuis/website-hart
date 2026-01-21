<?php
namespace app\tests\functional;

use app\models\ContactMessage;
use app\tests\fixtures\StaticContentFixture;
use app\tests\fixtures\TeacherFixture;
use app\tests\FunctionalTester;

class ContactFormVisibilityCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'static' => StaticContentFixture::class,
            'teachers' => TeacherFixture::class,
        ]);
    }

    public function testAssociationPageHasNoRadio(FunctionalTester $I)
    {
        $I->amOnPage('/vereniging');
        $I->see('Neem contact op met het bestuur', 'h3');
        $I->dontSee('Reden voor contact', 'label');
        $I->dontSeeElement('input[name="ContactMessage[type]"][type="radio"]');
        $I->seeElement('input[name="ContactMessage[type]"][type="hidden"]', ['value' => ContactMessage::TYPE_ORGANISATION_CONTACT]);
    }

    public function testContactPageHasNoRadio(FunctionalTester $I)
    {
        $I->amOnPage('/contact');
        $I->see('Algemeen contactformulier', 'h3');
        $I->dontSee('Reden voor contact', 'label');
        $I->dontSeeElement('input[name="ContactMessage[type]"][type="radio"]');
        $I->seeElement('input[name="ContactMessage[type]"][type="hidden"]', ['value' => ContactMessage::TYPE_GENERAL_CONTACT]);
    }

    public function testTeacherPageHasRadio(FunctionalTester $I)
    {
        $I->amOnPage('/docent/alice-van-dijk');
        $I->see('Neem contact op met Alice van Dijk', 'h3');
        $I->see('Reden voor contact', 'label');
        $I->seeElement('input[name="ContactMessage[type]"][type="radio"]');
    }
}
