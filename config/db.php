<?php
$host = 'localhost';
$db   = 'abogadoe_abogadoenlinea';      // <-- pon aquí el nombre EXACTO de tu BD
$user = 'abogadoe_abogadoenlineausr';    // <-- cambia por tu usuario MySQL
$pass = 'LauBon2026*.*';   // <-- cambia por tu contraseña MySQL
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    exit('Error de conexión a la base de datos');
}
