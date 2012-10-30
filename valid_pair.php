#!/usr/bin/env php
<?

/*
  Authing user with private and public key.
  1) User uploads public_key into database (key that he uses for SSH access).
  2) When authentication user sends only his private_key
  3) When authenticationg we take private_key from user
  then we build potential new public_key. Then we compare potential new public_key
  with public_keys already in database.

  * Matching private and public keys is known as pairing.
  * This could also be done using OpenSSL libs
  * ssh-keygen has bug so you cant get fingertip for faster matching.
*/

echo "valid_pair by <oto.brglez@dlabs.si>".PHP_EOL;

function valid_pair($private_key,$public_key){
	$generated_public_key = shell_exec(sprintf(
    "( echo '%s' | ssh-keygen -P '' -y -f /dev/stdin ) 2>&1",$private_key));

  if(strpos($generated_public_key,"ssh-") === false)
    return false;

  $key_a = trim(current(array_slice(split("\ ",$public_key),1,1)));
  $key_b = trim(current(array_slice(split("\ ",$generated_public_key),1,1)));

  if($key_a == $key_b)
    return true;

  return false;
};

echo valid_pair(
	file_get_contents("fake_rsa_key"),
  file_get_contents("fake_rsa_key.pub")
)? "Valid pair.".PHP_EOL : null;

echo !valid_pair(
  file_get_contents("fake_rsa_key_broken"),
	file_get_contents("fake_rsa_key.pub")
)? "Broken pair.".PHP_EOL : null;

echo valid_pair(
  file_get_contents("fake_dsa_key"),
	file_get_contents("fake_dsa_key.pub")
)? "Valid pair with DSA.".PHP_EOL : null;

