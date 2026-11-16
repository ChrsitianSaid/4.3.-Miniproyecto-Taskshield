# 4.3.-Miniproyecto-Taskshield

## Descripción

TaskShield es una pequeña intranet desarrollada en PHP y MySQL para la gestión de incidencias informáticas dentro de un departamento de soporte técnico.

La aplicación permite registrar incidencias, consultarlas, editarlas, eliminarlas y asignarlas a técnicos. Además, implementa medidas de seguridad para evitar inyecciones SQL y garantizar la integridad de los datos mediante transacciones.

---

## Tecnologías utilizadas

- PHP 8
- MySQL 
- PDO
- HTML5
- CSS3
- phpMyAdmin
- XAMPP

---

## Estructura del proyecto

```
TaskShield/
│
├── config.php
├── conexion.php
├── index.php
├── nueva_incidencia.php
├── editar_incidencia.php
├── eliminar_incidencia.php
├── asignar_incidencia.php
├── styles.css
│
└── docs/
    ├── schema.sql
    └── casos_prueba.md
```

---

## Funcionalidades

### Gestión de incidencias

- Crear incidencias.
- Consultar incidencias registradas.
- Editar incidencias existentes.
- Eliminar incidencias.

### Seguridad

- Uso de PDO.
- Uso de sentencias preparadas.
- Validación mediante `filter_input()`.
- Protección frente a inyecciones SQL.


---

## Instalación

1. Copiar el proyecto dentro de la carpeta `htdocs` de XAMPP.
2. Iniciar Apache y MySQL desde el panel de control de XAMPP.
3. Crear una base de datos llamada:

```sql
soporte_db
```

4. Importar el archivo:

```text
docs/schema.sql
```

5. Configurar las credenciales en `config.php`.
6. Acceder desde el navegador a:

```text
http://localhost/4.3.-Miniproyecto-Taskshield/
```

---

## Autores

- José Fernández
- Christian Said

---

## Estado del proyecto

Proyecto desarrollado como práctica de acceso a bases de datos mediante PDO en PHP.