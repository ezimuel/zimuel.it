<?php

$data = 'Hello World!';
$key  = random_bytes(16);

// GCM and CCM
$modes = ['aes-256-gcm', 'aes-256-ccm'];
foreach ($modes as $mode) {
  $iv = random_bytes(openssl_cipher_iv_length($mode));
  $ciphertext = openssl_encrypt($data, $mode, $key, OPENSSL_RAW_DATA, $iv, $tag);

  printf("Mode: %s, Tag: %s (%d)\n", $mode, base64_encode($tag), strlen($tag));
  $text = openssl_decrypt($ciphertext, $mode, $key, OPENSSL_RAW_DATA, $iv, $tag);

  printf("Decryption %s\n\n", $text === $data ? 'Ok' : 'Failed');
}
