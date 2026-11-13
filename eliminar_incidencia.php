<?php
    //1 Conectamos con la base de datos
    require_once 'conexion.php';

    //2. comprobamos si nos llega un ID válido por la URL
    if(isset($_GET['id'])){
        $id = $_GET['id'];

        try {
            //3. Preparamos y ejecutamos la sentencia DELETE de forma segura
            $stmt = $pdo->prepare("DELETE FROM incidencias WHERE id = ?");
            $stmt->execute([$id]);

            //4. Comprobacion estricta con rowCount():
            // Esto verifica si realmente se ha borrado una fila en la base de datos
            if ($stmt->rowCount() > 0) {
                //Borrado exitoso
                header("Location: index.php");
                exit;
            } else {
                //Si el ID no existe en la base de datos 
                echo "No se ha podido encontrar ninguna incidencia con ese ID.";
            } 

        } catch(PDOException $e) {
            die("Error al eliminar la incidencia: " . $e->getMessage());
        }
        
    } else {
        //Si alguien entra al archivo directamente sin pasar un ID por la URL
        header("Location: index.php");
        exit;
    }
?>