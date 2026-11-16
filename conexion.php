
<?php

    // conexion.php - Conexión segura con PDO

    // Comprobar que el acceso está permitido
    if (!defined('ACCESO_PERMITIDO')) {
        die("Acceso denegado");
    }

    require_once 'config.php';

    // Preparar la cadena de conexión con la base de datos
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    // Opciones de configuración de PDO
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Trabajar con excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false // Desactivar la emulación de consultas preparadas
    ];

    try {
        // Crear la conexión con la base de datos
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

    } catch (PDOException $e) {
        // Registrar el error técnico sin mostrar información interna al usuario
        error_log("Error de conexión PDO: " . $e->getMessage());

        die("No se ha podido establecer la conexión con la base de datos.");
    }

?>
