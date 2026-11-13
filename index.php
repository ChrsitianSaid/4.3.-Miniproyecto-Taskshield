<?php   
    define('ACCESO_PERMITIDO', true);
    require_once 'conexion.php'; //Conectamos el html con la bd mediante el archivo conexion.php

    try{
        $stmt = $pdo->prepare("SELECT id, titulo, descripcion, prioridad, estado, email_cliente FROM incidencias");
        $stmt->execute();

        $incidencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {

        die("Error al recuperar las incidencias: " . $e->getMessage()); 
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TaskShield</title>

    <link rel="stylesheet" href="styles.css">

</head>
<body>

<h1>Incidencias Activas</h1>

<table>

    <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Descripción</th>
        <th>Prioridad</th>
        <th>Estado</th>
        <th>Email Cliente</th>
        <th>Acciones</th>
    </tr>

    <?php if (empty($incidencias)): ?>
        <tr>
            <td colspan="7" id="incidencias"> no hay incidencias registradas </td>
        </tr>
    <?php else: ?>
            <?php foreach ($incidencias as $incidencia): ?>

        <tr>
            <td><?=htmlspecialchars($incidencia["id"]) ?></td>
            <td><?=htmlspecialchars($incidencia["titulo"]) ?></td>
            <td><?=htmlspecialchars($incidencia["descripcion"]) ?></td>
            <td><?=htmlspecialchars($incidencia["prioridad"]) ?></td>
            <td><?=htmlspecialchars($incidencia["estado"]) ?></td>
            <td><?=htmlspecialchars($incidencia["email_cliente"]) ?></td>
            <td>    
                <a href="editar_incidencia.php?id=<?= $incidencia["id"] ?>" class="btn-accion btn-editar">Editar</a>
                <a href="eliminar_incidencia.php?id=<?= $incidencia["id"] ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Estás seguro de eliminar esta incidencia?');">Eliminar</a>
            </td>
        </tr>

        <?php endforeach; ?>
    <?php endif; ?>

</table>

</body>
</html>