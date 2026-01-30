<?php
define('DB_HOST', 'https://ayar.dwm.ma/');
define('DB_NAME', '
ac_ayar_todo_list');
define('DB_USER', 'ac_ayar_todolist_user');
define('DB_PASS', 'Todolist_pass1');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME ,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>