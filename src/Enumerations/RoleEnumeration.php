<?php

namespace App\Enumerations;

use App\Exceptions\RoleNotFoundException;
use Eloquent\Enumeration\AbstractEnumeration;

class RoleEnumeration extends AbstractEnumeration
{
    const ROLE_OWNER = "ROLE_OWNER";
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_MODERATOR = "ROLE_MODERATOR";
    const ROLE_CREATOR = "ROLE_CREATOR";

    const NAME_OWNER = "Owner";
    const NAME_ADMIN = "Administrator";
    const NAME_MODERATOR = "Moderator";
    const NAME_CREATOR = "Creator";


    public static function getRole(string $string): string
    {
        return match(strtoupper($string)) {
            strtoupper(self::NAME_OWNER), self::ROLE_OWNER => self::ROLE_OWNER,
            strtoupper(self::NAME_ADMIN), self::ROLE_ADMIN => self::ROLE_ADMIN,
            strtoupper(self::NAME_MODERATOR), self::ROLE_MODERATOR => self::ROLE_MODERATOR,
            default => self::ROLE_CREATOR,
        };
    }

    public static function getRoleName(string $string): string
    {
        return match(strtoupper($string)) {
            strtoupper(self::NAME_OWNER), self::ROLE_OWNER => self::NAME_OWNER,
            strtoupper(self::NAME_ADMIN), self::ROLE_ADMIN => self::NAME_ADMIN,
            strtoupper(self::NAME_MODERATOR), self::ROLE_MODERATOR => self::NAME_MODERATOR,
            default => self::NAME_CREATOR,
        };
    }

    public static function getChoices(): array
    {
        return [
            self::NAME_ADMIN => self::ROLE_ADMIN,
            self::NAME_MODERATOR => self::ROLE_MODERATOR,
        ];
    }
}