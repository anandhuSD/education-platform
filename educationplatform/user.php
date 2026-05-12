<?php
// save as: create_user.php — run once in browser, then DELETE it
$username = "anandhu123";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$path = __DIR__ . "/users.txt";

// Read existing lines, remove old anandhu entry, add new one
$lines = [];
if (file_exists($path)) {
    $existing = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($existing as $line) {
        $parts = explode("|", $line);
        if (trim($parts[0]) !== "anandhu" && trim($parts[0]) !== "anandhu123") {
            $lines[] = $line; // keep other users
        }
    }
}

$lines[] = $username . "|" . $password;
file_put_contents($path, implode("\n", $lines) . "\n");

echo "Done! Username: anandhu123 / Password: admin123";
?>