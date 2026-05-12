<?php
$host   = "localhost";
$dbname = "educationplatform";
$user   = "root";
$pass   = ""; // your XAMPP MySQL password (usually empty)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("<div style='font-family:sans-serif;padding:40px;background:#fff5f5;border-left:4px solid #e74c3c;margin:20px;border-radius:8px;'>
        <h3 style='color:#e74c3c;margin:0 0 10px'>Database Connection Failed</h3>
        <p style='color:#555;margin:0'>" . $e->getMessage() . "</p>
        <p style='color:#888;font-size:13px;margin-top:10px'>Make sure XAMPP MySQL is running and the database <strong>educationplatform</strong> exists.</p>
    </div>");
}
?>