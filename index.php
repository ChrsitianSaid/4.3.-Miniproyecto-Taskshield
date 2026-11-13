<?php  
    define('ACCESO_PERMITIDO', true);
    require_once 'conexion.php'; 

    try{
        // Modificamos la consulta con un LEFT JOIN para obtener el nombre del técnico
        $sql = "SELECT i.id, i.titulo, i.descripcion, i.prioridad, i.estado, i.email_cliente, t.nombre AS nombre_tecnico 
                FROM incidencias i 
                LEFT JOIN tecnicos t ON i.id_tecnico = t.id";
                
        $stmt = $pdo->prepare($sql);
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
<div style="margin-bottom: 20px">
    <a href="nueva_incidencia.php" class="btn-accion" style="padding: 8px 12px; background-color: #4472C4; color: white; text-decoration: none; border-radius: 4px;">Nueva Incidencia</a>
    <a href="asignar_incidencia.php" class="btn-accion" style="padding: 8px 12px; background-color: #2e7d32; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">Asignar Avanzada</a>
</div>

<table>
    <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Descripción</th>
        <th>Prioridad</th>
        <th>Estado</th>
        <th>Email Cliente</th>
        <th>Técnico Asignado</th> <!-- Nueva columna -->
        <th>Acciones</th>
    </tr>

    <?php if (empty($incidencias)): ?>
        <tr>
            <td colspan="8" id="incidencias">No hay incidencias registradas</td>
        </tr>
    <?php else: ?>
        <?php foreach ($incidencias as $incidencia): ?>
            <tr>
                <td><?= htmlspecialchars($incidencia["id"]) ?></td>
                <td><?= htmlspecialchars($incidencia["titulo"]) ?></td>
                <td><?= htmlspecialchars($incidencia["descripcion"]) ?></td>
                <td><?= htmlspecialchars($incidencia["prioridad"]) ?></td>
                <td><?= htmlspecialchars($incidencia["estado"]) ?></td>
                <td><?= htmlspecialchars($incidencia["email_cliente"]) ?></td>
                <!-- Mostrar el técnico o un texto por defecto si es null -->
                <td><?= htmlspecialchars($incidencia["nombre_tecnico"] ?? 'Sin asignar') ?></td>
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