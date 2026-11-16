
<?php
    define('ACCESO_PERMITIDO', true);
    require_once 'conexion.php';


    $error = "";

    // Valores iniciales del formulario
    $titulo = "";
    $descripcion = "";
    $prioridad = "Media";
    $email_cliente = "";

    // 1. Comprueba si se ha enviado el formulario (petición POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Recoger los datos enviados mediante el formulario
        $tituloRecibido = filter_input(INPUT_POST, 'titulo', FILTER_UNSAFE_RAW);
        $descripcionRecibida = filter_input(INPUT_POST, 'descripcion', FILTER_UNSAFE_RAW);
        $prioridadRecibida = filter_input(INPUT_POST, 'prioridad', FILTER_UNSAFE_RAW);
        $emailRecibido = filter_input(INPUT_POST, 'email_cliente', FILTER_UNSAFE_RAW);

        // Limpiar espacios y conservar los valores introducidos
        $titulo = is_string($tituloRecibido) ? trim($tituloRecibido) : "";
        $descripcion = is_string($descripcionRecibida) ? trim($descripcionRecibida) : "";
        $prioridad = is_string($prioridadRecibida) ? trim($prioridadRecibida) : "";
        $email_cliente = is_string($emailRecibido) ? trim($emailRecibido) : "";

        // Validar el correo electrónico
        $email_validado = filter_input(INPUT_POST, 'email_cliente', FILTER_VALIDATE_EMAIL);

        try {
            // Comprobar que el título y la descripción no estén vacíos
            if ($titulo === "" || $descripcion === "") {
                throw new Exception("El título y la descripción son obligatorios.");
            }

            // Comprobar que la prioridad seleccionada sea válida
            if (!in_array($prioridad, ['Baja', 'Media', 'Alta'], true)) {
                throw new Exception("La prioridad seleccionada no es válida.");
            }

            // Comprobar que el correo electrónico tenga un formato correcto
            if ($email_validado === false || $email_validado === null) {
                throw new Exception("El correo electrónico no tiene un formato válido.");
            }

            // 2. Insertar la nueva incidencia en la base de datos
            $sql = "INSERT INTO incidencias (titulo, descripcion, prioridad, estado, email_cliente, fecha_creacion)
                    VALUES (:titulo, :descripcion, :prioridad, 'Pendiente', :email_cliente, NOW())";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':titulo' => $titulo,
                ':descripcion' => $descripcion,
                ':prioridad' => $prioridad,
                ':email_cliente' => $email_cliente
            ]);

            // 3. Obtener el ID generado automáticamente
            $nuevoId = $pdo->lastInsertId();

            // Redirigir al panel principal con el ID de la nueva incidencia
            header("Location: index.php?id=" . $nuevoId);
            exit;

        } catch (PDOException $e) {
            error_log("Error INSERT incidencia: " . $e->getMessage());
            $error = "No se ha podido registrar la incidencia.";

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskShield - Nueva Incidencia</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h1>Registrar Nueva Incidencia</h1>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <form action="nueva_incidencia.php" method="POST">

        <label for="titulo">Título:</label><br>
        <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8') ?>" maxlength="100" required><br><br>

        <label for="descripcion">Descripción:</label><br>
        <textarea id="descripcion" name="descripcion" required><?= htmlspecialchars($descripcion, ENT_QUOTES, 'UTF-8') ?></textarea><br><br>

        <label for="prioridad">Prioridad:</label><br>
        <select id="prioridad" name="prioridad" required>
            <option value="Baja" <?= $prioridad === 'Baja' ? 'selected' : '' ?>>Baja</option>
            <option value="Media" <?= $prioridad === 'Media' ? 'selected' : '' ?>>Media</option>
            <option value="Alta" <?= $prioridad === 'Alta' ? 'selected' : '' ?>>Alta</option>
        </select><br><br>

        <label for="email_cliente">Email del Cliente:</label><br>
        <input type="email" id="email_cliente" name="email_cliente" value="<?= htmlspecialchars($email_cliente, ENT_QUOTES, 'UTF-8') ?>" maxlength="100" required><br><br>

        <button type="submit">Guardar Incidencia</button>
        <a href="index.php">Cancelar</a>

    </form>

</body>
</html>
