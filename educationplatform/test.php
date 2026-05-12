<?php
$file = __DIR__ . "/users.txt";
$content = file_get_contents($file);
echo "File content: [" . $content . "]<br><br>";

list($stored_user, $stored_pass) = explode("|", trim($content), 2);
echo "Username: [" . $stored_user . "]<br>";
echo "Hash: [" . $stored_pass . "]<br><br>";

$result = password_verify("admin123", trim($stored_pass));
var_dump($result);
?>