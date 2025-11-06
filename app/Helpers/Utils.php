<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Utility helper for cryptography and UUID manipulation.
 *
 * This class provides secure AES-256-GCM encryption/decryption,
 * UUID format conversion, and key retrieval from configuration.
 *
 * @see https://www.php.net/manual/en/function.openssl-encrypt.php
 * @see https://datatracker.ietf.org/doc/html/rfc5288
 * @see https://www.php-fig.org/psr/psr-19/
 */
class Utils
{
    /**
     * Current encryption version tag used in the payload.
     * Helps maintain compatibility if algorithms change in the future.
     */
    private const ENCRYPTION_VERSION = "\x01";

    /**
     * Cipher algorithm and IV length constants.
     */
    private const CIPHER_METHOD = 'aes-256-gcm';
    private const IV_LENGTH = 12;
    private const TAG_LENGTH = 16;

    /**
     * Encrypts a string using AES-256-GCM with a random IV and version tag.
     *
     * @param string $plainText the plain text to be encrypted
     *
     * @throws \RuntimeException if encryption fails or the encryption key is invalid
     *
     * @return string binary output: VERSION + IV + TAG + ENCRYPTED_DATA
     *
     * @see https://www.php.net/manual/en/function.openssl-encrypt.php
     */
    public static function encrypt(string $plainText): string
    {
        $key = self::getEncryptionKey();

        if (empty($key)) {
            throw new \RuntimeException('Missing or invalid encryption key.');
        }

        $iv = random_bytes(self::IV_LENGTH);
        $tag = '';

        $encrypted = openssl_encrypt(
            $plainText,
            self::CIPHER_METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::TAG_LENGTH,
        );

        if ($encrypted === false) {
            throw new \RuntimeException('Failed to encrypt the data.');
        }

        return self::ENCRYPTION_VERSION . $iv . $tag . $encrypted;
    }

    /**
     * Decrypts data previously encrypted using AES-256-GCM.
     *
     * @param string $encryptedData binary input: VERSION + IV + TAG + ENCRYPTED_DATA
     *
     * @throws \RuntimeException if decryption fails or the data format is invalid
     *
     * @return string the decrypted plain text
     *
     * @see https://www.php.net/manual/en/function.openssl-decrypt.php
     */
    public static function decrypt(string $encryptedData): string
    {
        if (\strlen($encryptedData) < (1 + self::IV_LENGTH + self::TAG_LENGTH)) {
            throw new \RuntimeException('Invalid encrypted data length.');
        }

        $version = $encryptedData[0];

        if ($version !== self::ENCRYPTION_VERSION) {
            throw new \RuntimeException('Unsupported encryption version.');
        }

        $iv = substr($encryptedData, 1, self::IV_LENGTH);
        $tag = substr($encryptedData, 1 + self::IV_LENGTH, self::TAG_LENGTH);
        $cipherText = substr($encryptedData, 1 + self::IV_LENGTH + self::TAG_LENGTH);

        $key = self::getEncryptionKey();

        $decrypted = openssl_decrypt(
            $cipherText,
            self::CIPHER_METHOD,
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
        );

        if ($decrypted === false) {
            throw new \RuntimeException('Failed to decrypt the data.');
        }

        return $decrypted;
    }

    /**
     * Converts a UUID (hex string, with or without hyphens) to binary format.
     *
     * @param string $uuid UUID string (e.g., "123e4567-e89b-12d3-a456-426614174000").
     *
     * @throws \InvalidArgumentException if the UUID string is malformed
     *
     * @return null|string binary UUID or null if invalid
     */
    public static function convertUuidToBinary(string $uuid): ?string
    {
        $uuid = str_replace('-', '', trim($uuid));

        if (!ctype_xdigit($uuid) || \strlen($uuid) !== 32) {
            throw new \InvalidArgumentException('Invalid UUID format.');
        }

        $binary = @hex2bin($uuid);

        if ($binary === false) {
            throw new \RuntimeException('Failed to convert UUID to binary.');
        }

        return $binary;
    }

    /**
     * Converts a binary UUID to a string representation.
     *
     * @param string $binary binary UUID (16 bytes)
     *
     * @throws \InvalidArgumentException if the input is not a valid 16-byte binary UUID
     *
     * @return string UUID string (e.g., "123e4567-e89b-12d3-a456-426614174000").
     */
    public static function convertBinaryToUuid(string $binary): string
    {
        if (\strlen($binary) !== 16) {
            throw new \InvalidArgumentException('Invalid binary UUID length.');
        }

        $hex = bin2hex($binary);

        return \sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20),
        );
    }

    /**
     * Retrieves the system encryption key from the Laravel configuration.
     *
     * @throws \RuntimeException if the key is missing or not base64-encoded
     *
     * @return string the decoded encryption key (32 bytes for AES-256)
     *
     * @see https://laravel.com/docs/master/encryption
     */
    private static function getEncryptionKey(): string
    {
        $rawKey = config('app.encryption_key');

        if (!\is_string($rawKey) || empty($rawKey)) {
            throw new \RuntimeException('Encryption key not found in configuration.');
        }

        $key = base64_decode($rawKey, true);

        if ($key === false) {
            throw new \RuntimeException('Encryption key is not valid base64.');
        }

        return $key;
    }
}
