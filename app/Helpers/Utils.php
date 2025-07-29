<?php

namespace App\Helpers;

class Utils
{
    /**
     * Returns the system encryption key.
     *
     * @return string
     */
    private static function getEncryptionKey(): string
    {
        return base64_decode(config('app.encryption_key'));
    }

    /**
     * Encrypts a string using AES-256-GCM with a random IV.
     *
     * @param string $plainText The plain text to be encrypted.
     * @return string Binary output containing IV + TAG + encrypted data.
     */
    public static function encrypt(string $plainText): string
    {
        $key = self::getEncryptionKey();
        $cipherMethod = 'aes-256-gcm';
        $ivLength = openssl_cipher_iv_length($cipherMethod);
        $iv = random_bytes($ivLength);

        $tag = '';
        $encrypted = openssl_encrypt(
            $plainText,
            $cipherMethod,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        if ($encrypted === false) {
            throw new \RuntimeException('Failed to encrypt the data.');
        }

        return $iv . $tag . $encrypted;
    }

    /**
     * Decrypts data previously encrypted using AES-256-GCM.
     *
     * @param string $encryptedData Binary input containing IV + TAG + encrypted data.
     * @return string The original plain text.
     */
    public static function decrypt(string $encryptedData): string
    {
        $key = self::getEncryptionKey();
        $cipherMethod = 'aes-256-gcm';
        $ivLength = openssl_cipher_iv_length($cipherMethod);
        $tagLength = 16;

        if (strlen($encryptedData) < ($ivLength + $tagLength)) {
            throw new \RuntimeException('Invalid encrypted data.');
        }

        $iv = substr($encryptedData, 0, $ivLength);
        $tag = substr($encryptedData, $ivLength, $tagLength);
        $cipherText = substr($encryptedData, $ivLength + $tagLength);

        $decrypted = openssl_decrypt(
            $cipherText,
            $cipherMethod,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        if ($decrypted === false) {
            throw new \RuntimeException('Failed to decrypt the data.');
        }

        return $decrypted;
    }

    /**
     * Converts a UUID (hex string, with or without hyphens) to binary format.
     *
     * @param string $uuid UUID string (e.g., '123e4567-e89b-12d3-a456-426614174000').
     * @return string|null Binary UUID or null if invalid.
     */
    public static function convertUuidToBinary(string $uuid): ?string
    {
        $uuid = str_replace('-', '', trim($uuid));

        if (ctype_xdigit($uuid) && strlen($uuid) === 32) {
            return hex2bin($uuid);
        }

        return null;
    }
}
