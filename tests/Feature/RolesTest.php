<?php

namespace Tests\Feature;

use App\Role\UserRole;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\TF\TFHelpers;

class RolesTest extends TestCase
{
    use DatabaseMigrations;

    public $roles1 = [
        'Admin' => ['Moderator', 'Writer'],
        'Moderator' => ['Verified', 'User'],
        'Writer' => ['Verified', 'User'],
        'Verified' => [],
        'User' => []
    ];

    public $roles2 = [
        'Admin' => ['Mod1', 'Mod2', 'Mod3'],
        'Mod1' => ['r1', 'r2'],
        'Mod2' => ['r3', 'r4'],
        'Mod3' => ['r5', 'r6'],
    ];

    private static $defaultRoleHierarchy;

    public static function setUpBeforeClass(): void
    {
        self::$defaultRoleHierarchy = UserRole::getRoleHierarchy();
    }

    public function setUp(): void
    {
        parent::setUp();

        TFHelpers::runTestSeeders(); // not needed
    }

    public function testCanSetRoleHierarchy()
    {
        $this->assertNotEquals($this->roles1, UserRole::getRoleHierarchy());

        UserRole::setRoleHierarchy($this->roles1);

        $this->assertEquals($this->roles1, UserRole::getRoleHierarchy());
    }

    public function testCanGetRoleList()
    {
        UserRole::setRoleHierarchy($this->roles1);

        $roles = UserRole::getRolesNested('Admin');
        $allRoles = ['Admin', 'Moderator', 'Writer', 'Verified', 'User'];
        sort($roles);
        sort($allRoles);
        $this->assertEquals($allRoles, $roles);
    }

    public function testUserHasRoleSimple()
    {
        $user = User::factory()->create();

        $this->assertEmpty($user->getRolesFlat());

        $user->addRole('role1');
        $this->assertEquals(['role1'], $user->getRolesFlat());
        $this->assertTrue($user->hasRole('role1'));
    }

    public function testUserHasTwoRoles()
    {
        $user = User::factory()->create();

        $this->assertEmpty($user->getRolesFlat());
        $user->addRole('role1');
        $user->addRole('role2');
        $this->assertEquals(['role1', 'role2'], $user->getRolesFlat());
        $this->assertTrue($user->hasRole('role1'));
        $this->assertTrue($user->hasRole('role2'));
    }

    public function testUserHasNestedRoles()
    {
        UserRole::setRoleHierarchy($this->roles1);

        $user = User::factory()->create();

        $this->assertEmpty($user->getRolesFlat());

        $this->assertFalse($user->hasRole('Admin'));
        $this->assertFalse($user->hasRole('Moderator'));
        $this->assertFalse($user->hasRole('Writer'));
        $this->assertFalse($user->hasRole('User'));
        $this->assertFalse($user->hasRole('Verified'));

        $user->addRole('Admin');

        $this->assertTrue($user->hasRole('Admin'));
        $this->assertTrue($user->hasRole('Moderator'));
        $this->assertTrue($user->hasRole('Writer'));
        $this->assertTrue($user->hasRole('User'));
        $this->assertTrue($user->hasRole('Verified'));
    }

    public function testDifferentNestedRoles()
    {
        UserRole::setRoleHierarchy($this->roles2);

        $user = User::factory()->create();
        $this->assertEmpty($user->getRolesFlat());
        $user->addRole('Mod1');
        $user->addRole('Mod2');

        $this->assertTrue($user->hasRole('r2'));
        $this->assertTrue($user->hasRole('Mod2'));

        $this->assertFalse($user->hasRole('Mod3'));
        $this->assertFalse($user->hasRole('r6'));
    }

    public static function tearDownAfterClass(): void
    {
        UserRole::setRoleHierarchy(self::$defaultRoleHierarchy);
    }
}