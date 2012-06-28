<?php
include('../bootstrap.php');

/*
use FireKit\Utils\Security;

echo "Utils test-unit...\n";
echo Security::Password(20, "1234567890");
echo "\n";
*/


use FireKit\Utils\Transform;
echo "Utils test-unit for Transform::PhoneNumber...\n";
echo Transform::PhoneNumber("+380 (44) 390-8744");
echo "\n\n\n";



?>
