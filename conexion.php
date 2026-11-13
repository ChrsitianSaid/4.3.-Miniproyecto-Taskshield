<?php

    // conexion.php - Conexión segura con PDO
    require_once 'config.php';

    //si no existe la constante que solo define el index.php, bloqueemos el acceso
    if(!defined('ACCESO_PERMITIDO')) {
        die("Acceso denegado");
    }

    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // trabajar con exepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false, // Obligatorio: desactivar emulación y evita las inyecciones SQL
    ];

    try{
         $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        //En produccion se ocultaria el mensaje real, pero para desarrollo va bien 
        die("Error de conexión a la base de datos: ". $e->getMessage());
    };
?>