
<?php
    define('ACCESO_PERMITIDO', true);

    // 1. Conectamos con la base de datos
    require_once 'conexion.php';


    $error = "";

    // 2. Comprobamos si nos llega un ID por la URL
    if (isset($_GET['id'])) {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        try {
            // 3. Comprobamos que el ID recibido sea válido
            if ($id === false || $id === null || $id < 1) {
                throw new Exception("El ID de la incidencia no es válido.");
            }

            // 4. Preparamos y ejecutamos la sentencia DELETE de forma segura
            $stmt = $pdo->prepare("DELETE FROM incidencias WHERE id = ?");
            $stmt->execute([$id]);

            // 5. Comprobación estricta con rowCount()
            // Verifica si realmente se ha borrado una fila en la base de datos
            if ($stmt->rowCount() > 0) {
                // Borrado exitoso
                header("Location: index.php");
                exit;
            } else {
                // Si el ID no existe en la base de datos
                $error = "No se ha encontrado ninguna incidencia con ese ID.";
            }

        } catch (PDOException $e) {
            error_log("Error al eliminar incidencia: " . $e->getMessage());
            $error = "No se ha podido eliminar la incidencia.";

        } catch (Exception $e) {
            $error = $e->getMessage();
        }

    } else {
        // Si alguien entra al archivo directamente sin pasar un ID por la URL
        header("Location: index.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>TaskShield - Eliminar Incidencia</title>
</head>
<body>

    <h1>Eliminar Incidencia</h1>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <a href="index.php">Volver al panel principal</a>

</body>
</html>
        