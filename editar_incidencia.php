
<?php
    define('ACCESO_PERMITIDO', true);
    require_once 'conexion.php';


    $error = "";
    $mensaje = "";

    // Valores iniciales del formulario
    $incidencia = [
        'id' => '',
        'titulo' => '',
        'descripcion' => '',
        'prioridad' => '',
        'estado' => '',
        'email_cliente' => ''
    ];

    // Si la peticion no es GET ni POST, se redirige al index
    if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php");
        exit;
    }

    // Si es una peticion GET pero no tiene un ID en la URL
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['id'])) {
        header("Location: index.php");
        exit;
    }

    // 1. Comprueba si llega el ID por URL (petición GET para cargar el formulario)
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        try {
            // Comprobar que el ID recibido es válido
            if ($id === false || $id === null || $id < 1) {
                throw new Exception("El ID de la incidencia no es válido.");
            }

            // Consulta que busca la incidencia por su ID
            $stmt = $pdo->prepare("SELECT id, titulo, descripcion, prioridad, estado, email_cliente FROM incidencias WHERE id = ?");
            $stmt->execute([$id]);
            $incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si no existe ninguna incidencia con ese ID, se muestra un error
            if (!$incidencia) {
                $error = "La incidencia solicitada no existe.";
                $incidencia = [
                    'id' => '',
                    'titulo' => '',
                    'descripcion' => '',
                    'prioridad' => '',
                    'estado' => '',
                    'email_cliente' => ''
                ];
            }

        } catch (PDOException $e) {
            error_log("Error al cargar incidencia: " . $e->getMessage());
            $error = "No se pudo cargar la incidencia.";

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    // 2. Comprueba si se ha enviado el formulario (petición POST para actualizar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
        $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_SPECIAL_CHARS);
        $prioridad = filter_input(INPUT_POST, 'prioridad', FILTER_UNSAFE_RAW);
        $estado = filter_input(INPUT_POST, 'estado', FILTER_UNSAFE_RAW);
        $email_cliente = filter_input(INPUT_POST, 'email_cliente', FILTER_UNSAFE_RAW);
        $email_validado = filter_input(INPUT_POST, 'email_cliente', FILTER_VALIDATE_EMAIL);

        // Conservar los datos introducidos en el formulario
        $incidencia = [
            'id' => ($id !== false && $id !== null) ? $id : '',
            'titulo' => trim((string) $titulo),
            'descripcion' => trim((string) $descripcion),
            'prioridad' => trim((string) $prioridad),
            'estado' => trim((string) $estado),
            'email_cliente' => trim((string) $email_cliente)
        ];

        try {
            // Comprobar que el ID recibido es válido
            if ($id === false || $id === null || $id < 1) {
                throw new Exception("El ID de la incidencia no es válido.");
            }

            // Comprobar que el título y la descripción no están vacíos
            if ($incidencia['titulo'] === '' || $incidencia['descripcion'] === '') {
                throw new Exception("El título y la descripción son obligatorios.");
            }

            // Comprobar que el correo electrónico tiene un formato válido
            if ($email_validado === false || $email_validado === null) {
                throw new Exception("El correo electrónico no es válido.");
            }

            // Comprobar que la prioridad seleccionada es válida
            if (!in_array($prioridad, ['Baja', 'Media', 'Alta'], true)) {
                throw new Exception("La prioridad seleccionada no es válida.");
            }

            // Comprobar que el estado seleccionado es válido
            if (!in_array($estado, ['Pendiente', 'En proceso', 'Resuelto'], true)) {
                throw new Exception("El estado seleccionado no es válido.");
            }

            // Comprobar que existe una incidencia con ese ID
            $stmt = $pdo->prepare("SELECT id FROM incidencias WHERE id = ?");
            $stmt->execute([$id]);
            $comprobarIncidencia = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$comprobarIncidencia) {
                $incidencia['id'] = '';
                throw new Exception("La incidencia solicitada no existe.");
            }

            // Consulta que actualiza los datos de la incidencia
            $sql = "UPDATE incidencias SET titulo = ?, descripcion = ?, prioridad = ?, estado = ?, email_cliente = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $incidencia['titulo'],
                $incidencia['descripcion'],
                $incidencia['prioridad'],
                $incidencia['estado'],
                $incidencia['email_cliente'],
                $id
            ]);

            // Comprobar si se han realizado cambios en la incidencia
            if ($stmt->rowCount() > 0) {
                // Redirigir de vuelta al panel principal con los datos actualizados
                header("Location: index.php");
                exit;
            } else {
                $mensaje = "No se han realizado cambios en la incidencia.";
            }

        } catch (PDOException $e) {
            error_log("Error al actualizar incidencia: " . $e->getMessage());
            $error = "No se ha podido actualizar la incidencia.";

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
    <link rel="stylesheet" href="styles.css">
    <title>TaskShield - Editar Incidencia</title>
</head>
<body>

    <h1>Editar Incidencia #<?= htmlspecialchars((string) $incidencia['id'], ENT_QUOTES, 'UTF-8') ?></h1>

    <?php if (!empty($error)): ?>
        <p class="error"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if (!empty($mensaje)): ?>
        <p><?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>

    <?php if (!empty($incidencia['id'])): ?>

        <!-- El formulario envía los datos por POST a este mismo archivo -->
        <form action="editar_incidencia.php" method="POST">

            <!-- Campo oculto para no perder el ID de la incidencia que estamos editando -->
            <input type="hidden" name="id" value="<?= htmlspecialchars((string) $incidencia['id'], ENT_QUOTES, 'UTF-8') ?>">

            <label for="titulo">Título</label><br>
            <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($incidencia['titulo'], ENT_QUOTES, 'UTF-8', false) ?>" required><br><br>

            <label for="descripcion">Descripción</label><br>
            <textarea id="descripcion" name="descripcion" required><?= htmlspecialchars($incidencia['descripcion'], ENT_QUOTES, 'UTF-8', false) ?></textarea><br><br>

            <label for="prioridad">Prioridad:</label><br>
            <select id="prioridad" name="prioridad" required>
                <option value="Baja" <?= $incidencia['prioridad'] === 'Baja' ? 'selected' : '' ?>>Baja</option>
                <option value="Media" <?= $incidencia['prioridad'] === 'Media' ? 'selected' : '' ?>>Media</option>
                <option value="Alta" <?= $incidencia['prioridad'] === 'Alta' ? 'selected' : '' ?>>Alta</option>
            </select><br><br>

            <label for="estado">Estado</label><br>
            <select id="estado" name="estado" required>
                <option value="Pendiente" <?= $incidencia['estado'] === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="En proceso" <?= $incidencia['estado'] === 'En proceso' ? 'selected' : '' ?>>En proceso</option>
                <option value="Resuelto" <?= $incidencia['estado'] === 'Resuelto' ? 'selected' : '' ?>>Resuelto</option>
            </select><br><br>

            <label for="email_cliente">Email del Cliente</label><br>
            <input type="email" id="email_cliente" name="email_cliente" value="<?= htmlspecialchars($incidencia['email_cliente'], ENT_QUOTES, 'UTF-8') ?>" required><br><br>

            <button type="submit">Guardar Cambios</button>
            <a href="index.php">Cancelar</a>

        </form>

    <?php else: ?>

        <a href="index.php">Volver al panel principal</a>

    <?php endif; ?>

</body>
</html>
