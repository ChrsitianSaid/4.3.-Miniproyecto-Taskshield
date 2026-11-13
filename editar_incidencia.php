<?php
    define('ACCESO_PERMITIDO', true);
    require_once 'conexion.php';
    

    $error = "";
    $mensaje = "";

    // Si no es una peticion POST ni tiene un ID en la URL
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['id'])) {
        header("Location: index.php");
        exit;
    }
    
    // 1. Comprueba si llega el ID por URL (petición GET para cargar el formulario)
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        try {
            // Consulta que busca la incidencia por su ID
            $stmt = $pdo->prepare("SELECT id, titulo, descripcion, prioridad, estado, email_cliente FROM incidencias WHERE id = ?");
            $stmt->execute([$id]);
            $incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

            // Si no existe ninguna incidencia con ese ID, se redirigira al index
            if (!$incidencia) {
                header("Location: index.php");
                exit;
            }

        } catch (PDOException $e) {
            die("Error al cargar la incidencia: " . $e->getMessage());
        }
    }

    // 2. Comprueba si se ha enviado el formulario (es una peticion POST para actualizar)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $prioridad = $_POST['prioridad'];
        $estado = $_POST['estado'];
        $email_cliente = $_POST["email_cliente"];

        try {
            $sql = "UPDATE incidencias SET titulo = ?, descripcion = ?, prioridad = ?, estado = ?, email_cliente = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $descripcion, $prioridad, $estado, $email_cliente, $id]);

            // Redirigir de vuelta al panel principal con todo actualizado
            header("Location: index.php");
            exit;

        } catch(PDOException $e) {
            $error = "Error al actualizar la incidencia: " . $e->getMessage();
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
    
    <h1>Editar Incidencia #<?= htmlspecialchars($incidencia['id']); ?></h1>

    <?php if(!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <!-- El formulario envía los datos por POST a este mismo archivo -->
    <form action="editar_incidencia.php" method="POST">
        <!-- Campo oculto para no perder el ID de la incidencia que estamos editando -->
        <input type="hidden" name="id" value="<?= htmlspecialchars($incidencia['id']) ?>">

        <label for="titulo">Título</label><br>
        <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($incidencia['titulo']) ?>" required><br><br>

        <label for="descripcion">Descripción</label><br>
        <textarea id="descripcion" name="descripcion" required><?= htmlspecialchars($incidencia['descripcion']) ?></textarea><br><br>

        <label for="prioridad">Prioridad:</label><br>
        <select id="prioridad" name="prioridad">
            <option value="Baja" <?= $incidencia['prioridad'] === 'Baja' ? 'selected' : '' ?>>Baja</option>
            <option value="Media" <?= $incidencia['prioridad'] === 'Media' ? 'selected' : '' ?>>Media</option>
            <option value="Alta" <?= $incidencia['prioridad'] === 'Alta' ? 'selected' : '' ?>>Alta</option>
        </select><br><br>

        <label for="estado">Estado</label><br>
        <select id="estado" name="estado">
            <option value="Pendiente" <?= $incidencia['estado'] === 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
            <option value="En proceso" <?= $incidencia['estado'] === 'En proceso' ? 'selected' : '' ?>>En proceso</option>
            <option value="Resuelto" <?= $incidencia['estado'] === 'Resuelto' ? 'selected' : '' ?>>Resuelto</option>
        </select><br><br>

        <label for="email_cliente">Email del Cliente</label><br>
        <input type="email" id="email_cliente" name="email_cliente" value="<?= htmlspecialchars($incidencia['email_cliente']) ?>" required><br><br>

        <button type="submit">Guardar Cambios</button>
        <a href="index.php">Cancelar</a>
    </form>
</body>
</html>