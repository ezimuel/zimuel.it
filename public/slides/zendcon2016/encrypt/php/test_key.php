<?php

//EVP_CIPHER_key_length

openssl_encrypt(
  'test',
  'aes-256-cbc',
  'a',
  OPENSSL_RAW_DATA,
  random_bytes(openssl_cipher_iv_length('aes-256-cbc'))
);
