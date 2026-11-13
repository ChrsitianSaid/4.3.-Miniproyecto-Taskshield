<?php
    define('ACCESO_PERMITIDO', true);
    
    // asignar_incidencia.php - Módulo de Asignación Avanzada con Transacciones
    require_once 'conexion.php';

    $mensaje = "";
    $error = "";

    // 1. Obtener los técnicos y las incidencias en estado 'Pendiente' para los desplegables
    try {
        $stmtTecnicos = $pdo->query("SELECT id, nombre, tareas_activas FROM tecnicos");
        $tecnicos = $stmtTecnicos->fetchAll(PDO::FETCH_ASSOC);

        $stmtInc = $pdo->query("SELECT id, titulo FROM incidencias WHERE estado = 'Pendiente'");
        $incidenciasPendientes = $stmtInc->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Error al cargar los datos iniciales: " . $e->getMessage();
    }

    // 2. Procesar el formulario cuando se envía por POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_incidencia = filter_input(INPUT_POST, 'id_incidencia', FILTER_VALIDATE_INT);
        $id_tecnico = filter_input(INPUT_POST, 'id_tecnico', FILTER_VALIDATE_INT);

        if ($id_incidencia && $id_tecnico) {
            try {
                // INICIAR LA TRANSACCIÓN (Paso crucial de la parte 4)
                $pdo->beginTransaction();

                // Comprobación previa: verificar que el técnico realmente existe
                $checkTecnico = $pdo->prepare("SELECT id FROM tecnicos WHERE id = ?");
                $checkTecnico->execute([$id_tecnico]);
                if (!$checkTecnico->fetch()) {
                    throw new Exception("El técnico seleccionado no existe en el sistema.");
                }

                // Primera operación: Actualizar la incidencia (pasar a 'En proceso' y asignar id_tecnico)
                $sqlUpdInc = "UPDATE incidencias SET estado = 'En proceso', id_tecnico = ? WHERE id = ? AND estado = 'Pendiente'";
                $stmtUpdInc = $pdo->prepare($sqlUpdInc);
                $stmtUpdInc->execute([$id_tecnico, $id_incidencia]);

                if ($stmtUpdInc->rowCount() === 0) {
                    throw new Exception("No se pudo actualizar la incidencia (puede que ya no esté pendiente).");
                }

                // Segunda operación: Incrementar en +1 el contador 'tareas_activas' del técnico
                $sqlUpdTec = "UPDATE tecnicos SET tareas_activas = tareas_activas + 1 WHERE id = ?";
                $stmtUpdTec = $pdo->prepare($sqlUpdTec);
                $stmtUpdTec->execute([$id_tecnico]);

                if ($stmtUpdTec->rowCount() === 0) {
                    throw new Exception("Error al actualizar el contador de tareas del técnico.");
                }

                // SI TODO VA BIEN: Aplicar los cambios de forma permanente
                $pdo->commit();
                $mensaje = "¡Incidencia asignada correctamente y contador del técnico actualizado!";

                // Recargar las listas desplegables para reflejar el estado actual
                $stmtTecnicos = $pdo->query("SELECT id, nombre, tareas_activas FROM tecnicos");
                $tecnicos = $stmtTecnicos->fetchAll(PDO::FETCH_ASSOC);

                $stmtInc = $pdo->query("SELECT id, titulo FROM incidencias WHERE estado = 'Pendiente'");
                $incidenciasPendientes = $stmtInc->fetchAll(PDO::FETCH_ASSOC);

            } catch (Exception $e) {
                // SI ALGO FALLA: Deshacer todo lo hecho en esta transacción (rollBack)
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error = "Transacción cancelada (Rollback ejecutado). Motivo: " . $e->getMessage();
            }
        } else {
            $error = "Por favor, seleccione una incidencia y un técnico válidos.";
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TaskShield - Asignación Avanzada</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <h1>Asignación Avanzada de Incidencias</h1>
    <p style="text-align: center;">Módulo protegido mediante <strong>Transacciones PDO (beginTransaction / commit / rollBack)</strong></p>

    <?php if (!empty($mensaje)): ?>

    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="error" style="text-align: center; font-weight: bold;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form action="asignar_incidencia.php" method="POST" style="max-width: 500px; margin: 0 auto; background: #f9f9f9; padding: 20px; border: 1px solid #ddd;">
        
        <label for="id_incidencia">Seleccionar Incidencia Pendiente:</label><br>
        <select name="id_incidencia" id="id_incidencia" required style="width: 100%; padding: 8px; margin-bottom: 15px;">
            <option value="">-- Selecciona una incidencia --</option>
            <?php foreach ($incidenciasPendientes as $inc): ?>
                <option value="<?= $inc['id'] ?>">#<?= $inc['id'] ?> - <?= htmlspecialchars($inc['titulo']) ?></option>
            <?php endforeach; ?>
        </select><br>

        <label for="id_tecnico">Seleccionar Técnico:</label><br>
        <select name="id_tecnico" id="id_tecnico" required style="width: 100%; padding: 8px; margin-bottom: 15px;">
            <option value="">-- Selecciona un técnico --</option>
            <?php foreach ($tecnicos as $tec): ?>
                <option value="<?= $tec['id'] ?>">
                    <?= htmlspecialchars($tec['nombre']) ?> (Tareas activas: <?= $tec['tareas_activas'] ?>)
                </option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit" style="padding: 10px 15px; background: #4472C4; color: white; border: none; cursor: pointer;">Asignar Incidencia</button>
        <a href="index.php" style="margin-left: 15px; text-decoration: none; color: #333;">Volver al Panel</a>
    </form>

</body>
</html>