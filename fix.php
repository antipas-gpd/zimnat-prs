<?php
$hash = password_hash('Admin1234', PASSWORD_BCRYPT, ['cost' => 12]);
echo $hash;