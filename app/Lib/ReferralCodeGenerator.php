<?php

namespace App\Lib;

use App\Models\User;

class ReferralCodeGenerator
{
    protected static $validChars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    protected static $codeLength = 8;

    public static function generateUniqueCode()
    {
        $code = '';
        $validCharsLength = strlen(self::$validChars);

        if (function_exists('random_bytes')) {
            $rnd = random_bytes(self::$codeLength);
            for ($i = 0; $i < self::$codeLength; ++$i) {
                $code .= self::$validChars[ord($rnd[$i]) % $validCharsLength];
            }
        } else {
            for ($i = 0; $i < self::$codeLength; ++$i) {
                $code .= self::$validChars[random_int(0, $validCharsLength - 1)];
            }
        }

        return $code;
    }

    public static function generateUniqueReferralCode(): string
    {
        do {
            $referralCode = self::generateUniqueCode();
        } while (User::query()->where('referral_code', $referralCode)->exists());

        return $referralCode;
    }
}
