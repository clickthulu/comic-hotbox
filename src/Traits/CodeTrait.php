<?php

namespace App\Traits;

trait CodeTrait
{
    private function generateCode($length = 16): string
    {
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($length / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
        } else {
            return "";
        }
        return substr(bin2hex($bytes), 0, $length);
    }



}