<?php

namespace App\Enumerations;

use App\Exceptions\RoleNotFoundException;

class RoleEnumeration extends \Eloquent\Enumeration\AbstractEnumeration
{
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_CREATOR = "ROLE_CREATOR";


    public static function getRole(string $string): string
    {
        switch(strtoupper($string)){
            case "ADMIN":
            case self::ROLE_ADMIN:
                return self::ROLE_ADMIN;
            case "CREATOR":
            case self::ROLE_CREATOR:
                return self::ROLE_CREATOR;
        }
        throw new RoleNotFoundException("Role {$string} not found");
    }
}