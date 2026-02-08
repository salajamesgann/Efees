<?php

namespace App\Services;

class SmsTemplates
{
    public static function getNewAccountMessage(string $username, string $password): string
    {
        return "Welcome to E-Fees! Your parent account is ready.\nUsername: {$username}\nPassword: {$password}\nLogin to view fees.";
    }

    public static function getPasswordResetMessage(string $password): string
    {
        return "Your E-Fees password has been reset. New password: {$password}";
    }

    public static function getEnrollmentConfirmationMessage(string $studentName): string
    {
        return "Enrollment confirmed for {$studentName}. You can now view fees in your parent portal.";
    }
}
