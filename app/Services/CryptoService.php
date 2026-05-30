<?php

namespace App\Services;

class CryptoService
{
    // Secret phrase used to derive a secure 32-byte key
    protected static $secret = 'e-voting-academic-research-secret-2026';

    /**
     * Encrypts plaintext string using AES-256-GCM
     * @param string $text Plaintext to encrypt
     * @return array Encryption packet (ciphertext, iv, tag)
     */
    public static function encrypt($text)
    {
        try {
            $method = 'aes-256-gcm';
            // Derive a secure 32-byte binary key
            $key = hash('sha256', self::$secret, true);
            $iv_len = openssl_cipher_iv_length($method);
            $iv = openssl_random_pseudo_bytes($iv_len);
            
            $tag = '';
            $ciphertext = openssl_encrypt(
                $text,
                $method,
                $key,
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );

            return [
                'success' => true,
                'ciphertext' => bin2hex($ciphertext),
                'iv' => bin2hex($iv),
                'tag' => bin2hex($tag)
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Decrypts AES-256-GCM encrypted packet
     * @param string $ciphertextHex Ciphertext in hex
     * @param string $ivHex Initialization vector in hex
     * @param string $tagHex Auth tag in hex
     * @return string Plaintext decrypted value
     */
    public static function decrypt($ciphertextHex, $ivHex, $tagHex)
    {
        try {
            $method = 'aes-256-gcm';
            $key = hash('sha256', self::$secret, true);
            $ciphertext = hex2bin($ciphertextHex);
            $iv = hex2bin($ivHex);
            $tag = hex2bin($tagHex);

            $decrypted = openssl_decrypt(
                $ciphertext,
                $method,
                $key,
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );

            if ($decrypted === false) {
                return '[Decryption Error: Invalid Key or Corrupted Data]';
            }

            return $decrypted;
        } catch (\Exception $e) {
            return '[Decryption Error: ' . $e->getMessage() . ']';
        }
    }
}
