<?php

namespace app\tests\functional;

use app\models\User;
use app\tests\fixtures\UserFixture;

use app\tests\FunctionalTester;

class UserFlowCest
{
    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => codecept_data_dir() . '/user.php',
            ],
        ]);
    }

    public function testAdminCannotChangeOtherUserPassword(FunctionalTester $I)
    {
        $I->amLoggedInAs(1); // Assuming admin
        $I->amOnPage('/user/update?id=2'); // Assuming user 2 is not admin
        $I->dontSee('Wachtwoord', 'label');
        $I->dontSeeElement('#user-password');
    }

    public function testUserCanChangeOwnPassword(FunctionalTester $I)
    {
        $I->amLoggedInAs(2);
        $I->amOnPage('/user/update?id=2');
        $I->see('Wachtwoord', 'label');
    }

    public function testPasswordResetRequest(FunctionalTester $I)
    {
        $I->amOnPage('/site/login');
        $I->seeLink('Wachtwoord vergeten?');
        $I->click('Wachtwoord vergeten?');
        $I->see('Wachtwoord herstellen');
        $I->fillField('E-mail', 'user@example.com');
        $I->click('Verzenden');
        
        // The message should intentionally not tell whether a user with that email address exists.
        $I->see('Controleer je e-mail voor verdere instructies.');
    }

    public function testAdminCanSendActivationEmail(FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        $I->amOnPage('/user/admin');
        
        // Find an inactive user or make one inactive
        /** @var User $user */
        $user = User::findOne(2);
        $user->is_active = false;
        $user->generateActivationToken();
        $user->save(false);
        $token = $user->activation_token;

        $I->amOnPage('/user/update?id=2');
        $I->see('Activatie-e-mail verzenden');
        $I->click('Activatie-e-mail verzenden');
        $I->see('Activatielink verzonden.');

        $user->refresh();
        $token = $user->activation_token;

        // Test activation
        $I->amOnPage('/site/activate?token=' . $token);
        $I->see('Je account is geactiveerd! Je kunt nu inloggen.');
        
        $user->refresh();
        $I->assertTrue($user->is_active);
        $I->assertNull($user->activation_token);
    }

    public function testAdminCanSendPasswordResetEmail(FunctionalTester $I)
    {
        $I->amLoggedInAs(1);
        $I->amOnPage('/user/update?id=2');
        $I->see('Wachtwoord-reset-e-mail verzenden');
    }
}
