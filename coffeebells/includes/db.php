<?php
// ============================================================
// DB.PHP — Conexión PDO Singleton
// ============================================================

function getDB(): PDO {
    static $pdo = null;

    if ($pdo !== null) return $pdo;

    $config = [
        'host'    => 'localhost',
        'dbname'  => 'coffeebells',
        'charset' => 'utf8mb4',
        'user'    => 'root',
        'pass'    => '',
        'port'    => '3306',
    ];

    // Permite override con archivo .env.php si existe
    $env_file = __DIR__ . '/../.env.php';
    if (file_exists($env_file)) {
        $env = require $env_file;
        $config = array_merge($config, $env);
    }

    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";

    try {
        $pdo = new PDO($dsn, $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        // En producción: loguear y mostrar error genérico
        error_log('DB Error: ' . $e->getMessage());
        http_response_code(503);
        die('<h1 style="font-family:sans-serif;color:#c62828;padding:2rem;">Error de conexión. Intenta más tarde.</h1>');
    }

    return $pdo;
}