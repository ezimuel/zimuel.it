<?php

function encrypt(string $text, string $key): string {
  $iv      = random_bytes(16); // iv size for aes-256-cbc
  $keys    = hash_pbkdf2('sha256', $key, $iv, 80000, 64, true);
  $encKey  = mb_substr($keys, 0, 32, '8bit');
  $hmacKey = mb_substr($keys, 32, null, '8bit');

  $ciphertext = openssl_encrypt($text, 'aes-256-cbc', $encKey,
    OPENSSL_RAW_DATA, $iv
  );

  $hmac = hash_hmac('sha256', $iv . $ciphertext, $hmacKey);
  return $hmac . $iv . $ciphertext;
}

function decrypt(string $text, string $key): string {
  $hmac       = mb_substr($text, 0, 64, '8bit');
  $iv         = mb_substr($text, 64, 16, '8bit');
  $ciphertext = mb_substr($text, 80, null, '8bit');

  $keys    = hash_pbkdf2('sha256', $key, $iv, 80000, 64, true);
  $encKey  = mb_substr($keys, 0, 32, '8bit');
  $hmacKey = mb_substr($keys, 32, null, '8bit');
  $hmacNow = hash_hmac('sha256', $iv . $ciphertext, $hmacKey);
  if (! hash_equals($hmac, $hmacNow)) {
    throw new Exception('Authentication error!');
  }
  return openssl_decrypt($ciphertext, 'aes-256-cbc', $encKey,
    OPENSSL_RAW_DATA, $iv
  );
}

$text = "Hello World!";
$key  = '1234567890';
$encrypted = encrypt($text, $key);
var_dump($encrypted);
$decrypt = decrypt($encrypted, $key);
var_dump($decrypt);
