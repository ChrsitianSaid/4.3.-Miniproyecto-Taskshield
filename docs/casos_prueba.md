# Casos de Prueba - TaskShield

## Caso de Prueba 1: Introducción de email inválido

### Objetivo
Verificar que el sistema valida correctamente el formato del correo electrónico al crear una incidencia.

### Datos de entrada
- Título: Problema con impresora
- Descripción: La impresora no responde.
- Prioridad: Alta
- Email: correo_invalido

### Resultado esperado
El sistema debe rechazar el formulario y mostrar un mensaje de error indicando que el correo electrónico no es válido.

### Resultado obtenido
La incidencia no se registra y se muestra el mensaje de validación correspondiente.

### Estado
Correcto 

---

## Caso de Prueba 2: Intento de edición de una incidencia inexistente

### Objetivo
Comprobar que el sistema controla correctamente los intentos de edición de incidencias que no existen.

### Datos de entrada
Acceso directo mediante URL:

editar_incidencia.php?id=99999

### Resultado esperado
El sistema detecta que la incidencia no existe y redirige al usuario al panel principal sin producir errores.

### Resultado obtenido
La incidencia no se carga y el usuario es redirigido correctamente al panel principal.

### Estado
Correcto 

---

## Caso de Prueba 3: Fallo durante una transacción de asignación

### Objetivo
Verificar que el sistema ejecuta correctamente un rollback cuando falla una de las operaciones de la transacción.

### Datos de entrada
- Incidencia pendiente existente.
- Técnico inexistente o error forzado en la segunda actualización.

### Resultado esperado
La transacción debe cancelarse completamente mediante `rollBack()`. La incidencia debe mantener su estado original y el contador de tareas activas del técnico no debe modificarse.

### Resultado obtenido
La transacción se cancela correctamente y los datos permanecen consistentes en la base de datos.

### Estado
Correcto 

---

## Conclusión

Las pruebas realizadas verifican el correcto funcionamiento de las validaciones de entrada, el control de errores y la integridad de los datos mediante el uso de transacciones PDO.