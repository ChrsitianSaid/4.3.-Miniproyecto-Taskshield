
<?php
    define('ACCESO_PERMITIDO', true);

    // asignar_incidencia.php - Módulo de Asignación Avanzada con Transacciones
    require_once 'conexion.php';


    $mensaje = "";
    $error = "";

    $tecnicos = [];
    $incidenciasPendientes = [];

    $id_incidenciaSeleccionada = "";
    $id_tecnicoSeleccionado = "";

    // 1. Procesar el formulario cuando se envía por POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_incidencia = filter_input(INPUT_POST, 'id_incidencia', FILTER_VALIDATE_INT);
        $id_tecnico = filter_input(INPUT_POST, 'id_tecnico', FILTER_VALIDATE_INT);

        // Conservar las selecciones realizadas
        $id_incidenciaSeleccionada = ($id_incidencia !== false && $id_incidencia !== null) ? $id_incidencia : "";
        $id_tecnicoSeleccionado = ($id_tecnico !== false && $id_tecnico !== null) ? $id_tecnico : "";

        try {
            // Comprobar que los identificadores recibidos sean válidos
            if ($id_incidencia === false || $id_incidencia === null || $id_incidencia < 1 ||
                $id_tecnico === false || $id_tecnico === null || $id_tecnico < 1) {
                throw new Exception("Por favor, seleccione una incidencia y un técnico válidos.");
            }

            // 2. Iniciar la transacción
            $pdo->beginTransaction();

            // Comprobación previa: verificar que el técnico realmente existe
            $checkTecnico = $pdo->prepare("SELECT id FROM tecnicos WHERE id = ?");
            $checkTecnico->execute([$id_tecnico]);

            if (!$checkTecnico->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("El técnico seleccionado no existe en el sistema.");
            }

            // 3. Primera operación: actualizar la incidencia
            // Cambiar su estado a 'En proceso' y asignar el técnico
            $sqlUpdInc = "UPDATE incidencias SET estado = 'En proceso', id_tecnico = ? WHERE id = ? AND estado = 'Pendiente'";
            $stmtUpdInc = $pdo->prepare($sqlUpdInc);
            $stmtUpdInc->execute([$id_tecnico, $id_incidencia]);

            if ($stmtUpdInc->rowCount() === 0) {
                throw new Exception("No se pudo actualizar la incidencia (puede que ya no esté pendiente).");
            }

            // 4. Segunda operación: incrementar las tareas activas del técnico
            $sqlUpdTec = "UPDATE tecnicos SET tareas_activas = tareas_activas + 1 WHERE id = ?";
            $stmtUpdTec = $pdo->prepare($sqlUpdTec);
            $stmtUpdTec->execute([$id_tecnico]);

            if ($stmtUpdTec->rowCount() === 0) {
                throw new Exception("Error al actualizar el contador de tareas del técnico.");
            }

            // 5. Si ambas operaciones funcionan, confirmar los cambios
            $pdo->commit();
            $mensaje = "¡Incidencia asignada correctamente y contador del técnico actualizado!";

            // Limpiar las selecciones después de una asignación correcta
            $id_incidenciaSeleccionada = "";
            $id_tecnicoSeleccionado = "";

        } catch (PDOException $e) {
            // Si algo falla, deshacer los cambios de la transacción
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log("Error en la transacción de asignación: " . $e->getMessage());
            $error = "No se ha podido completar la asignación. Se han deshecho los cambios.";

        } catch (Exception $e) {
            // Si algo falla, deshacer los cambios de la transacción
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $error = $e->getMessage();
        }
    }

    // 6. Recuperar los técnicos y las incidencias pendientes para los desplegables
    try {
        $stmtTecnicos = $pdo->query("SELECT id, nombre, tareas_activas FROM tecnicos");
        $tecnicos = $stmtTecnicos->fetchAll(PDO::FETCH_ASSOC);

        $stmtInc = $pdo->query("SELECT id, titulo FROM incidencias WHERE estado = 'Pendiente'");
        $incidenciasPendientes = $stmtInc->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        error_log("Error al cargar los datos de asignación: " . $e->getMessage());
        $error = "No se han podido cargar los datos para realizar la asignación.";
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
    

    <?php if (!empty($mensaje)): ?>
        <p style="text-align: center; font-weight: bold;">
            <?= htmlspecialchars($mensaje, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="error" style="text-align: center; font-weight: bold;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form action="asignar_incidencia.php" method="POST" style="max-width: 500px; margin: 0 auto; background: #f9f9f9; padding: 20px; border: 1px solid #ddd;">

        <label for="id_incidencia">Seleccionar Incidencia Pendiente:</label><br>
        <select name="id_incidencia" id="id_incidencia" required style="width: 100%; padding: 8px; margin-bottom: 15px;">
            <option value="">-- Selecciona una incidencia --</option>
            <?php foreach ($incidenciasPendientes as $inc): ?>
                <option value="<?= $inc['id'] ?>" <?= (string) $id_incidenciaSeleccionada === (string) $inc['id'] ? 'selected' : '' ?>>
                    #<?= $inc['id'] ?> - <?= htmlspecialchars($inc['titulo'], ENT_QUOTES, 'UTF-8') ?>
                </option>
            <?php endforeach; ?>
        </select><br>

        <label for="id_tecnico">Seleccionar Técnico:</label><br>
        <select name="id_tecnico" id="id_tecnico" required style="width: 100%; padding: 8px; margin-bottom: 15px;">
            <option value="">-- Selecciona un técnico --</option>
            <?php foreach ($tecnicos as $tec): ?>
                <option value="<?= $tec['id'] ?>" <?= (string) $id_tecnicoSeleccionado === (string) $tec['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($tec['nombre'], ENT_QUOTES, 'UTF-8') ?> (Tareas activas: <?= $tec['tareas_activas'] ?>)
                </option>
            <?php endforeach; ?>
        </select><br>

        <button type="submit" style="padding: 10px 15px; background: #4472C4; color: white; border: none; cursor: pointer;">
            Asignar Incidencia
        </button>

        <a href="index.php" style="margin-left: 15px; text-decoration: none; color: #333;">Volver al Panel</a>

    </form>

</body>
</html>
