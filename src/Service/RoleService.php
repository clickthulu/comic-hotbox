<?php

namespace App\Service;

use App\Enumerations\RoleEnumeration;

class RoleService
{

    public function rolename(string $string): string
    {
        return RoleEnumeration::getRoleName($string);
    }

    public function role(string $string): string
    {
        return RoleEnumeration::getRole($string);
    }

    public function list(): array
    {
        return [
            RoleEnumeration::ROLE_OWNER => RoleEnumeration::NAME_OWNER,
            RoleEnumeration::ROLE_ADMIN => RoleEnumeration::NAME_ADMIN,
            RoleEnumeration::ROLE_MODERATOR => RoleEnumeration::NAME_MODERATOR,
            RoleEnumeration::ROLE_CREATOR => RoleEnumeration::NAME_CREATOR,
        ];
    }

}