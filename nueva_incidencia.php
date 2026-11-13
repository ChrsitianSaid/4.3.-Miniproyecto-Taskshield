<?php
    require_once 'conexion.php';

    $error = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
        $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_SPECIAL_CHARS);
        $prioridad = filter_input(INPUT_POST, 'prioridad', FILTER_SANITIZE_SPECIAL_CHARS);
        $email_cliente = filter_input(INPUT_POST, 'email_cliente', FILTER_VALIDATE_EMAIL);

        // Validación lógica
        if (!$titulo || !$email_cliente) {
            $error = "Por favor, introduce un título válido y un correo electrónico con formato correcto.";
        } else {
            try {
                $sql = "INSERT INTO incidencias (titulo, descripcion, prioridad, estado, email_cliente, fecha_creacion) 
                        VALUES (:titulo, :descripcion, :prioridad, 'Pendiente', :email_cliente, NOW())";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':titulo' => $titulo,
                    ':descripcion' => $descripcion,
                    ':prioridad' => $prioridad,
                    ':email_cliente' => $email_cliente
                ]);

                // Obtener el ID generado automáticamente 
                $nuevoId = $pdo->lastInsertId();

                header("Location: index.php");
                exit;

            } catch (PDOException $e) {
                error_log("Error INSERT incidencia: " . $e->getMessage());
                $error = "No se ha podido registrar la incidencia.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TaskShield - Nueva Incidencia</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
    <h1>Registrar Nueva Incidencia</h1>

    <?php if(!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form action="nueva_incidencia.php" method="POST">
        <label for="titulo">Título:</label><br>
        <input type="text" id="titulo" name="titulo" required><br><br>

        <label for="descripcion">Descripción:</label><br>
        <textarea id="descripcion" name="descripcion" required></textarea><br><br>

        <label for="prioridad">Prioridad:</label><br>
        <select id="prioridad" name="prioridad">
            <option value="Baja">Baja</option>
            <option value="Media" selected>Media</option>
            <option value="Alta">Alta</option>
        </select><br><br>

        <label for="email_cliente">Email del Cliente:</label><br>
        <input type="email" id="email_cliente" name="email_cliente" required><br><br>

        <button type="submit">Guardar Incidencia</button>
        <a href="index.php">Cancelar</a>
    </form>

</body>
</html>