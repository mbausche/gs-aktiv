<?php

include 'funktionen.php';

try {
    echo formatAsCurrency(10.00) . "\n";
} catch (Exception $e) {
    echo 'Exception abgefangen: ',  $e->getMessage(), "\n";
}

echo phpinfo();


?>