<?php

namespace App\Helpers;

class Validators
{
    /**
     * Validates a Brazilian CPF number.
     *
     * @param string $cpf The CPF number as a string.
     * @return bool True if the CPF is valid, false otherwise.
     */
    public static function validateCpf(string $cpf): bool
    {
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += $cpf[$i] * (($t + 1) - $i);
            }

            $digit = ($sum * 10) % 11;
            if ($digit == 10) $digit = 0;

            if ($cpf[$t] != $digit) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validates the structure of a course name.
     *
     * @param string $course The course name to validate.
     * @return bool True if the course name is valid, false otherwise.
     */
    public static function validateCourseName(string $course): bool
    {
        $course = trim($course);

        if (preg_match("/^[A-Za-zÀ-ÖØ-öø-ÿ' -]+$/", $course)) {
            return strlen($course) >= 3 && strlen($course) <= 100;
        }

        return false;
    }

    /**
     * Validates the structure of a password.
     *
     * Password must:
     * - Be 8 to 16 characters long
     * - Include at least one uppercase letter
     * - Include at least one lowercase letter
     * - Include at least one digit
     * - Include at least one special character
     *
     * @param string $password The password to validate.
     * @return bool True if the password meets the criteria, false otherwise.
     */
    public static function validatePasswordStructure(string $password): bool
    {
        $regex = '/^
        (?=.*[a-z])                              # At least one lowercase letter
        (?=.*[A-Z])                              # At least one uppercase letter
        (?=.*\d)                                 # At least one digit
        (?=.*[!@#$%^&(),.?":{}|<>])              # At least one special character
        [A-Za-z\d!@#$%^&(),.?":{}|<>]{8,16}      # Length between 8 and 16
        $/x';

        return (bool) preg_match($regex, $password);
    }

    /**
     * Validates the structure of a person's full name.
     * Name must:
     * - Contain only letters, spaces, hyphens or apostrophes
     * - Have at least two words
     * - Be between 3 and 100 characters
     *
     * @param string $name The full name to validate.
     * @return bool True if the name is valid, false otherwise.
     */
    public static function validateFullName(string $name): bool
    {
        $name = trim($name);

        if (!preg_match("/^[\p{L} '-]+$/u", $name)) {
            return false;
        }

        $words = array_filter(explode(' ', $name));

        return count($words) >= 2 && mb_strlen($name) >= 3 && mb_strlen($name) <= 100;
    }

    /**
     * Validates a Brazilian mobile phone number.
     *
     * The number must:
     * - Have 11 digits (including area code)
     * - Start with a valid DDD (area code)
     * - Start with 9 after the DDD (mobile numbers)
     *
     * @param string $number The phone number to validate.
     * @return bool True if the phone number is valid, false otherwise.
     */
    public static function validatePhoneNumber(string $number): bool
    {
        $number = preg_replace('/\D/', '', $number);

        if (strlen($number) !== 11) {
            return false;
        }

        $areaCode = substr($number, 0, 2);
        $firstDigit = substr($number, 2, 1);

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
            '64',
            '63',
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
            '99'
        ];

        if (!in_array($areaCode, $validAreaCodes)) {
            return false;
        }

        if ($firstDigit !== '9') {
            return false;
        }

        return true;
    }

    /**
     * Validates the structure of an email address using PHP's filter and DNS check.
     *
     * @param string $email The email to validate.
     * @return bool True if the email is valid and domain has MX records, false otherwise.
     */
    public static function validateEmail(string $email): bool
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $domain = substr(strrchr($email, "@"), 1);

        return checkdnsrr($domain, "MX");
    }

    /**
     * Validates an ISBN-10 or ISBN-13.
     *
     * @param string $isbn The ISBN to validate.
     * @return bool True if the ISBN is valid, false otherwise.
     */
    public static function validateIsbn(string $isbn): bool
    {
        $isbn = preg_replace('/\D/', '', $isbn);

        if (strlen($isbn) === 10) {
            $sum = 0;
            for ($i = 0; $i < 9; $i++) {
                if (!is_numeric($isbn[$i])) {
                    return false;
                }
                $sum += (int)$isbn[$i] * (10 - $i);
            }

            $check = strtoupper($isbn[9]);
            $sum += ($check === 'X') ? 10 : (int)$check;

            return $sum % 11 === 0;
        } else if (strlen($isbn) === 13) {
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += (int)$isbn[$i] * ($i % 2 === 0 ? 1 : 3);
            }

            $checkDigit = (10 - ($sum % 10)) % 10;

            return (int)$isbn[12] === $checkDigit;
        }

        return false;
    }
}
