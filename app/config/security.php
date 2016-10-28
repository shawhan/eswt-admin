<?php

$security = new \Phalcon\Security();
//Set the password hashing factor to 5000 rounds
$security->setWorkFactor(5000);
#$security->setDefaultHash(\Phalcon\Security::CRYPT_SHA512);

return $security;

