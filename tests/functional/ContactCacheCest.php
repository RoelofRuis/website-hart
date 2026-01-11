<?php
namespace app\tests\functional;

use app\models\ContactMessage;
use app\tests\fixtures\ContactMessageFixture;
use app\tests\fixtures\StaticContentFixture;
use app\tests\FunctionalTester;

class ContactCacheCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'contacts' => ContactMessageFixture::class,
            'static' => StaticContentFixture::class,
        ]);
    }

    public function testFlashMessageBypassesCache(FunctionalTester $I)
    {
        // 1. Visit contact page, should have caching headers
        $I->amOnPage('/contact');
        $headers = \Yii::$app->response->headers;
        $I->assertEquals('public, max-age=600, stale-while-revalidate=60', $headers->get('Cache-Control'));
        $I->assertNotEmpty($headers->get('ETag'));

        // 2. Submit form
        $I->submitForm('form', [
            'ContactMessage[name]' => 'Jan Jansen',
            'ContactMessage[email]' => 'jan@example.com',
            'ContactMessage[message]' => 'Hallo!',
            'ContactMessage[type]' => ContactMessage::TYPE_TEACHER_CONTACT,
        ]);

        // 3. After redirect, we should see the flash message
        $I->seeCurrentUrlEquals('/contact');
        $I->see('Dank, je bericht is verstuurd!');

        // 5. Check headers after redirect
        $headers = \Yii::$app->response->headers;
        $I->assertFalse($headers->has('Cache-Control') || $headers->get('Cache-Control') === 'no-cache', 'Cache-Control should not be public when flash is present');
        $I->assertFalse($headers->has('ETag'), 'ETag should not be set when flash is present');
    }
}
