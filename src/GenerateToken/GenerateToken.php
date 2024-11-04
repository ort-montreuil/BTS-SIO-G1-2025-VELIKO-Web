<?php

namespace App\GenerateToken;

class GenerateToken
{
    public static function generateNewToken($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }
}
