<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); 
ini_set('session.cookie_samesite', 'Lax');


if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}


if (!isset($_SESSION['dark_mode'])) {
    $_SESSION['dark_mode'] = false;
}

if (!isset($_SESSION['filters'])) {
    $_SESSION['filters'] = [
        'projet' => null,
        'priorite' => null,
        'statut' => null,
        'etiquette' => null
    ];
}
?>
