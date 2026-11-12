<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>TaskShield</title>

    <style>

body {
    font-family: Arial, sans-serif;
    margin: 20px;
}

h1 {
    text-align: center;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th {
    background-color: #4472C4;
    color: white;
}

th, td {
    border: 1px solid #ddd;
    padding: 10px;
}

tr:nth-child(even) {
    background-color: #f2f2f2;
}

tr:hover {
    background-color: #e6e6e6;
}

</style>

</head>
<body>

<h1>Incidencias Activas</h1>

<?php

$incidencias = [
    [
        "id" => 1,
        "titulo" => "Ordenador no enciende",
        "descripcion" => "Pantalla negra al arrancar",
        "prioridad" => "Alta",
        "estado" => "Pendiente",
        "email" => "cliente1@test.com"
    ],
    [
        "id" => 2,
        "titulo" => "Impresora atascada",
        "descripcion" => "No imprime documentos",
        "prioridad" => "Media",
        "estado" => "En Proceso",
        "email" => "cliente2@test.com"
    ]
];

?>

<table>

    <tr>
        <th>ID</th>
        <th>Título</th>
        <th>Descripción</th>
        <th>Prioridad</th>
        <th>Estado</th>
        <th>Email Cliente</th>
    </tr>

    <?php foreach ($incidencias as $incidencia): ?>

    <tr>
        <td><?= $incidencia["id"] ?></td>
        <td><?= $incidencia["titulo"] ?></td>
        <td><?= $incidencia["descripcion"] ?></td>
        <td><?= $incidencia["prioridad"] ?></td>
        <td><?= $incidencia["estado"] ?></td>
        <td><?= $incidencia["email"] ?></td>
    </tr>

    <?php endforeach; ?>

</table>

</body>
</html>