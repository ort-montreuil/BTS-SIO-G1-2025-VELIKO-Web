<?php

namespace App\GenerateToken;

class GenerateToken
{
    // Générer un token aléatoire
    public static function generateNewToken($length = 32)
    {
        // Génère un token aléatoire de 32 caractères (256 bits) en utilisant la fonction random_bytes
        return bin2hex(random_bytes($length / 2));
    }
}
