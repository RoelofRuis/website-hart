<?php

namespace app\tests\functional;

use app\models\Changelog;
use app\models\User;
use app\tests\fixtures\ChangelogFixture;
use app\tests\fixtures\UserFixture;
use app\tests\FunctionalTester;
use app\tests\UnitTester;
use Yii;
use function PHPUnit\Framework\assertArrayHasKey;
use function PHPUnit\Framework\assertArrayNotHasKey;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;
use function PHPUnit\Framework\assertTrue;

class ChangelogBehaviorCest
{
    protected UnitTester $tester;

    public function _before(FunctionalTester $I)
    {
        $I->haveFixtures([
            ['class' => ChangelogFixture::class, 'dataFile' => false],
            ['class' => UserFixture::class],
        ]);
    }

    public function testChangelogCreatedOnUpdate()
    {
        $user = new User();
        $user->full_name = 'Original Name';
        $user->email = 'test@example.com';
        $user->password_hash = 'hash';
        assertTrue($user->save(), 'User should be saved');

        // Initial save should not create a changelog entry (it's EVENT_AFTER_UPDATE)
        assertEquals(0, Changelog::find()->count());

        // Update user
        $user->full_name = 'New Name';
        assertTrue($user->save(), 'User update should be saved');

        // Check changelog
        $log = Changelog::find()->one();
        assertNotNull($log, 'Changelog entry should be created');
        assertEquals(User::class, $log->model_class);
        assertEquals((string)$user->id, $log->model_id);
        
        $changes = $log->changes;
        assertArrayHasKey('full_name', $changes);
        assertEquals('Original Name', $changes['full_name']['old']);
        assertEquals('New Name', $changes['full_name']['new']);
    }

    public function testExcludeAttributes()
    {
        $user = new User();
        $user->full_name = 'Test User';
        $user->email = 'test2@example.com';
        $user->password_hash = 'old_hash';
        $user->save();

        // Update excluded attribute
        $user->password_hash = 'new_hash';
        $user->save();

        // Should not create changelog entry because password_hash is excluded
        assertEquals(0, Changelog::find()->count(), 'Excluded attributes should not trigger changelog');
        
        // Update both
        $user->full_name = 'Changed Name';
        $user->password_hash = 'another_hash';
        $user->save();
        
        $log = Changelog::find()->one();
        assertNotNull($log);
        assertArrayHasKey('full_name', $log->changes);
        assertArrayNotHasKey('password_hash', $log->changes);
    }

    public function testNoChangeNoLog()
    {
        $user = new User();
        $user->full_name = 'Static Name';
        $user->email = 'static@example.com';
        $user->password_hash = 'hash';
        $user->save();

        // Save without changes
        $user->save();

        assertEquals(0, Changelog::find()->count(), 'No changes should not create changelog entry');
    }

    public function testChangedBy()
    {
        // We need the mock user to exist in DB because of foreign key constraint
        $mockUser = new User();
        $mockUser->id = 999;
        $mockUser->full_name = 'Mock Admin';
        $mockUser->email = 'mock@example.com';
        $mockUser->password_hash = 'h';
        $mockUser->save(false);

        $user = new User();
        $user->full_name = 'Admin';
        $user->email = 'admin@example.com';
        $user->password_hash = 'hash';
        $user->save();

        // Mock a web application user session
        Yii::$app->user->setIdentity($mockUser);

        $user->full_name = 'Changed by Admin';
        $user->save();

        $log = Changelog::find()->where(['model_id' => (string)$user->id])->one();
        assertNotNull($log);
        assertEquals(999, $log->changed_by);
        
        // Cleanup identity
        Yii::$app->user->logout();
    }
}
