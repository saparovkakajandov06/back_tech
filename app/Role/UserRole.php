<?php

namespace App\Role;

/***
 * Class UserRole
 * @package App\Role
 */
class UserRole {

    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_MODERATOR = 'ROLE_MODERATOR';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_SEO = 'ROLE_SEO';
    const ROLE_PROXY = 'ROLE_PROXY';
    const ROLE_VERIFIED = 'ROLE_VERIFIED';
    const ROLE_AUTO = 'ROLE_AUTO';
    // next 2 exist nowhere
    const ROLE_BLOGGER = 'ROLE_BLOGGER';
    const ROLE_SUPPORT = 'ROLE_SUPPORT';

    const ROLE_ANY = 'ROLE_ANY';

    /**
     * @var array
     */
    public static $roleHierarchy = [
        self::ROLE_ADMIN => [
            self::ROLE_MODERATOR,
            self::ROLE_MANAGER,
            self::ROLE_SEO,
            self::ROLE_PROXY
        ],
        self::ROLE_MODERATOR => [
            self::ROLE_MANAGER,
            self::ROLE_PROXY,
            self::ROLE_VERIFIED,
        ],
        self::ROLE_MANAGER => [
            self::ROLE_VERIFIED,
        ],
        self::ROLE_SEO => [
            self::ROLE_VERIFIED,
        ],
        self::ROLE_PROXY => [
            self::ROLE_VERIFIED,
        ],
        self::ROLE_VERIFIED => [],
        self::ROLE_AUTO => [],

        self::ROLE_ANY => [], // for documentation
    ];

    public static function setRoleHierarchy(array $roleHierarchy)
    {
        self::$roleHierarchy = $roleHierarchy;
    }

    public static function getRoleHierarchy(): array
    {
        return self::$roleHierarchy;
    }

    public static function getRolesNested(string $role, array $known_roles = []): array
    {
        $roles = [$role];
        if(isset(self::$roleHierarchy[$role])) {
            foreach(self::$roleHierarchy[$role] as $r) {
                if (!in_array($r, $known_roles)) {
                    $roles = array_merge($roles, static::getRolesNested($r, $roles));
                }
            }
        }
        return array_unique($roles);
    }
}
