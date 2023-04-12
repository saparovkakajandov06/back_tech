<?php

namespace App\Documentor;

use App\Exceptions\NonReportable\AttributeException;
use App\Role\UserRole;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Role implements Plural
{
    private string $role;

    public function __construct($role)
    {
        if (empty($role)) {
            throw new AttributeException('Empty role');
        }

        $roles = array_keys(UserRole::$roleHierarchy);
        if (! in_array($role, $roles)) {
            throw new AttributeException('Bad role: ' . $role);
        }

        $this->role = $role;
    }

    public function getKey()
    {
        return 'roles';
    }

    public function getValue()
    {
        return $this->role;
    }
}
