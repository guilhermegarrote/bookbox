<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Utility class providing static validation methods for various data formats.
 *
 * This helper centralizes all validation logic used throughout the system,
 * ensuring consistency and reusability for user input, identifiers, and business rules.
 */
class Validators
{
    /**
     * Validates a Brazilian CPF number.
     *
     * @param string $cpf the CPF number as a string (formatted or not)
     *
     * @return bool true if the CPF is valid; otherwise, false
     */
    public static function validateCpf(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (\strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; ++$t) {
            $sum = 0;
            for ($i = 0; $i < $t; ++$i) {
                $sum += (int) $cpf[$i] * (($t + 1) - $i);
            }

            $digit = ($sum * 10) % 11;
            $digit = ($digit === 10) ? 0 : $digit;

            if ((int) $cpf[$t] !== $digit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validates the structure of a course name.
     *
     * @param string $course the course name to validate
     *
     * @return bool true if the name meets the format requirements
     */
    public static function validateCourseName(string $course): bool
    {
        $course = trim($course);

        return preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$/", $course)
            && \strlen($course) >= 3
            && \strlen($course) <= 100;
    }

    /**
     * Validates a password structure according to system security rules.
     *
     * Password must:
     * - Be 8–16 characters long.
     * - Include at least one uppercase and one lowercase letter.
     * - Include at least one digit and one special character.
     *
     * @param string $password the password string to validate
     *
     * @return bool true if the password meets the security requirements
     *
     * @see https://owasp.org/www-community/password-special-characters
     */
    public static function validatePasswordStructure(string $password): bool
    {
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,16}$/';

        return (bool) preg_match($regex, $password);
    }

    /**
     * Validates a full name according to format and length rules.
     *
     * Name must contain:
     * - Only letters, spaces, hyphens or apostrophes.
     * - At least two words.
     * - Between 3 and 100 characters.
     *
     * @param string $name the full name to validate
     *
     * @return bool true if valid, false otherwise
     */
    public static function validateFullName(string $name): bool
    {
        $name = trim($name);

        if (!preg_match("/^[\\p{L} '-]+$/u", $name)) {
            return false;
        }

        $words = array_filter(explode(' ', $name));

        return \count($words) >= 2
            && mb_strlen($name) >= 3
            && mb_strlen($name) <= 100;
    }

    /**
     * Validates a Brazilian mobile phone number.
     *
     * Rules:
     * - 11 digits (including DDD)
     * - Valid DDD
     * - Must start with 9 after the DDD
     *
     * @param string $number the phone number to validate
     *
     * @return bool true if valid; otherwise, false
     */
    public static function validatePhoneNumber(string $number): bool
    {
        $number = preg_replace('/\D/', '', $number);

        if (\strlen($number) !== 11) {
            return false;
        }

        $areaCode = substr($number, 0, 2);
        $firstDigit = $number[2];

        $validAreaCodes = [
            '11',
            '12',
            '13',
            '14',
            '15',
            '16',
            '17',
            '18',
            '19',
            '21',
            '22',
            '24',
            '27',
            '28',
            '31',
            '32',
            '33',
            '34',
            '35',
            '37',
            '38',
            '41',
            '42',
            '43',
            '44',
            '45',
            '46',
            '47',
            '48',
            '49',
            '51',
            '53',
            '54',
            '55',
            '61',
            '62',
            '63',
            '64',
            '65',
            '66',
            '67',
            '68',
            '69',
            '71',
            '73',
            '74',
            '75',
            '77',
            '79',
            '81',
            '82',
            '83',
            '84',
            '85',
            '86',
            '87',
            '88',
            '89',
            '91',
            '92',
            '93',
            '94',
            '95',
            '96',
            '97',
            '98',
            '99',
        ];

        return \in_array($areaCode, $validAreaCodes, true) && $firstDigit === '9';
    }

    /**
     * Validates the structure of an email address and checks MX DNS records.
     *
     * @param string $email the email address to validate
     *
     * @return bool true if valid and domain has MX records; otherwise, false
     */
    public static function validateEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = substr(strrchr($email, '@'), 1);

        return $domain && checkdnsrr($domain, 'MX');
    }

    /**
     * Validates an ISBN-10 or ISBN-13 format and checksum.
     *
     * @param string $isbn the ISBN to validate
     *
     * @return bool true if valid; otherwise, false
     *
     * @see https://en.wikipedia.org/wiki/International_Standard_Book_Number
     */
    public static function validateIsbn(string $isbn): bool
    {
        $isbn = preg_replace('/\D/', '', $isbn);

        if (\strlen($isbn) === 10) {
            $sum = 0;
            for ($i = 0; $i < 9; ++$i) {
                $sum += (int) $isbn[$i] * (10 - $i);
            }

            $check = strtoupper($isbn[9]);
            $sum += ($check === 'X') ? 10 : (int) $check;

            return $sum % 11 === 0;
        }

        if (\strlen($isbn) === 13) {
            $sum = 0;
            for ($i = 0; $i < 12; ++$i) {
                $sum += (int) $isbn[$i] * (($i % 2 === 0) ? 1 : 3);
            }

            $checkDigit = (10 - ($sum % 10)) % 10;

            return (int) $isbn[12] === $checkDigit;
        }

        return false;
    }

    /**
     * Validates a loan code in the format LNXXXXXXXX.
     *
     * Accepts both the full code (LN + 8 alphanumeric characters)
     * or partial prefixes (for autocomplete use cases).
     *
     * @param string $code the loan code to validate
     *
     * @return bool true if valid; otherwise, false
     */
    public static function validateLoanCode(string $code): bool
    {
        $code = strtoupper(preg_replace('/[^A-Z0-9]/', '', $code));

        return (bool) preg_match('/^LN[A-Z0-9]{0,8}$/', $code);
    }
}
