<?php
    require_once 'conexion.php';

    $error = "";
    $mensaje = "";

    //Si no es una peticion POST ni tiene un ID en la URL
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['id'])) {
        header("Location: index.php");
        exit;
    }
    
    //1. Conprueb a si llega el ID por URL (etición GET para cargar el formulario)
    if (isset($_GE['id'])) {
        $id = $_GET['id'];

        try {
            //consulta que busca la incidencia por su ID
            $stmt = $pdo->prepare("SELECT id, titulo, descripcion, prioridad, estado, email_cliente FROM incidencias WHERE id = ?");
            $stmt->execute([$id]);
            $incidencia = $stmt->fetch(PDO::FETCH_ASSOC);

            //Si no existe ninguna incidencia con ese ID, se redirigira al index
            if (!$incidencia) {
                header("Location: index.php");
                exit;
            }

        } catch (PDOException $e) {
            die("Error al cargar la incidencia: ") . $e->getMessage();
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
            //Sentencia preparada UPDATE para actualizar los datos de forma segura 
            $sql = "UPDATE incidencia SET titulo = ?, descripcion = ?, prioirdad= ?, estado = ?, email_cliente =? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$titulo, $descripcion, $prioridad, $estado, $email_cliente, $id]);

            //Redirigir de vuelta al paner principal con todo actualizado
            header("location: index.php");
            exit;

        }catch(PDOException $e) {
            die("Error al actualizar la incidencia: " . $e->getMessage());
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Taskshild-Editar_incidencias</title>
</head>
<body>
    
    <h1>Editar Incidencias #<?= htmlspecialchars($incidencia['id']); ?> ?></h1>

    <?php if(!empty($error)): ?>
        <p id="error"><?=  $error ?></p>
    <?php endif; ?>

    <!-- el formulario envía los datos por POST a este miso archivo -->
     <form activo="editar_incidencia.php" method="POST">
        <!--Campo oculto para no perder el ID de la incidencia que estamos editando -->
        <input type="hidden" name="id" value="<?= htmlspecialchars($incidencia['id']) ?>">

        <label for="titulo">Titulo</label><br>
        <input type="text" id="titulo" name="titulo" value="<?= htmlentities($incidencia['titulo']) ?>" required><br><br>

        <label for="descripcion">Descripcion</label><br>
        <textarea id="descripcion" name="descripcion" required><?=  htmlspecialchars($incidencia['descripcion']) ?></textarea><br><br>

        <label for="prioridad">Prioridad:</label><br>
        <select id="prioridad" name="prioridad">
            <option value="Baja" <?=  $incidencia['prioridad'] === 'Baja' ? 'selected' :''?>>Baja</option>
            <option value="Media" <?=  $incidencia['prioridad'] === 'Media' ? 'selected' :''?>>Media</option>
            <option value="Alta" <?=  $incidencia['prioridad'] === 'Alta' ? 'selected' :''?>>Alta</option>
        </select><br><br>

        <label for="estado">Estado</label><br>
        <select id="estado" name="estado">
            <option value="Pendiente" <?=  $incidencia['estado'] === 'Pendiente' ? 'selected' : ''?>>Pendiente</option>
            <option value="En proceso" <?=  $incidencia['estado'] === 'En proceso' ? 'selected' : ''?>>En proceso</option>
            <option value="Resuelto" <?=  $incidencia['estado'] === 'Resuelto' ? 'selected' : ''?>>Resuelto</option>
        </select><br><br>

        <label for="email_cliente">email del Cliente</label><br>
        <input type="email" id="email_cliente" name="email_cliente" value="<?= htmlspecialchars($incidencia['email_cliente']) ?>" required><br><br>

        <button type="submit">Guardar Cambio</button>
        <a href="index.php">Cancelar</a>

    </form>
</body>
</html>