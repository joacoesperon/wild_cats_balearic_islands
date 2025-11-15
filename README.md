# Proyecto de Gestión de Colonias Felinas - Mallorca

Este proyecto implementa una aplicación web para la gestión de colonias de gatos comunitarios en Mallorca, siguiendo los requisitos de la "Opción 1" del enunciado. La aplicación está desarrollada en PHP nativo con MySQL y Apache, diseñada para ser portable y funcionar en un entorno XAMPP.

## Características Implementadas

-   **Base de Datos**: `gatos_mallorca.sql` con todas las tablas, datos de prueba, un `STORED PROCEDURE` para registro de backups, un `EVENT` para su ejecución diaria y un `TRIGGER` para la gestión automática de estancias de gatos.
-   **Aplicación Web**:
    -   PHP nativo, estructura MVC ligera.
    -   Autenticación de usuarios (Ayuntamientos y Voluntarios).
    -   CRUD completo para Colonias, Gatos, Grupos, Voluntarios (bolsín), Visitas, Incidencias y **Tareas (Trabajos)**.
    -   Subida de fotos para gatos.
    -   Uso de PDO para consultas preparadas.
    -   Funcionalidad de backup manual de la base de datos, con detección de sistema operativo para `mysqldump`.
    -   Funcionalidad para que los voluntarios puedan ver sus grupos, tareas y visitas asignadas, y marcar tareas como completadas.
-   **Portabilidad**: Diseñado para funcionar directamente al copiar la carpeta en `xampp/htdocs/`.
-   **Seguridad**: **NOTA IMPORTANTE: Por requisitos del enunciado, la seguridad es 0. No hay validación de entrada, contraseñas en texto plano, ni protección CSRF/HTTPS.**

## Requisitos del Sistema

-   Servidor web Apache
-   PHP 7.4 o superior (con extensión `pdo_mysql` habilitada)
-   MySQL 5.7 o superior
-   XAMPP (recomendado para una configuración sencilla)

## Configuración del Proyecto

Sigue estos pasos para poner en marcha la aplicación:

1.  **Clonar o Descargar el Proyecto**:
    Copia la carpeta `practica2` (o el nombre de tu proyecto) en el directorio `htdocs` de tu instalación de XAMPP (ej. `C:\xampp\htdocs\` en Windows, `/Applications/XAMPP/htdocs/` en macOS, o `/opt/lampp/htdocs/` en Linux).

2.  **Configurar la Base de Datos**:
    a.  Inicia Apache y MySQL desde el panel de control de XAMPP.
    b.  Abre tu navegador y ve a `http://localhost/phpmyadmin/`.
    c.  Importa el archivo `sql/gatos_mallorca.sql`:
        -   Haz clic en la pestaña "Importar".
        -   Haz clic en "Seleccionar archivo" y busca `gatos_mallorca.sql` dentro de la carpeta `sql` de tu proyecto.
        -   Asegúrate de que el conjunto de caracteres del archivo sea `utf8mb4`.
        -   Haz clic en "Continuar" para iniciar la importación.
        -   Esto creará la base de datos `gatos_mallorca` con todas las tablas, datos de prueba, el `STORED PROCEDURE`, el `EVENT` y el `TRIGGER`.

3.  **Configuración de la Aplicación (Automática)**:
    El archivo `config/database.php` está diseñado para detectar automáticamente si la aplicación se ejecuta en `localhost` y usar las credenciales por defecto de XAMPP (`root` sin contraseña). También creará las carpetas `public/uploads/gatos` y `backups` si no existen.

4.  **Acceder a la Aplicación**:
    Abre tu navegador y ve a `http://localhost/practica2/` (o el nombre de la carpeta de tu proyecto). Serás redirigido a la página de login.

## Usuarios de Prueba

Para acceder a la aplicación, puedes usar los siguientes usuarios de prueba (definidos en `gatos_mallorca.sql`):

### Ayuntamientos

| Usuario      | Contraseña |
| :----------- | :--------- |
| `palma_user` | `palma123` |
| `calvia_user`| `calvia123`|
| `manacor_user`| `manacor123`|

### Voluntarios

| Usuario      | Contraseña |
| :----------- | :--------- |
| `jperez`     | `pass123`  |
| `mlopez`     | `pass123`  |
| `csanchez`   | `pass123`  |
| `agomez`     | `pass123`  |
| `prodriguez` | `pass123`  |
| `lmartinez`  | `pass123`  |
| `dgarcia`    | `pass123`  |
| `sruiz`      | `pass123`  |
| `jfernandez` | `pass123`  |
| `ediaz`      | `pass123`  |

## Notas sobre el STORED PROCEDURE de Backup

El `STORED PROCEDURE` `sp_backup_gatos_mallorca` y el `EVENT` `daily_backup_event` están definidos en `gatos_mallorca.sql`. El `EVENT` programa la ejecución diaria del `STORED PROCEDURE`.

**Aclaración sobre la generación de backups:**
Debido a limitaciones de seguridad de MySQL, un `STORED PROCEDURE` no puede generar directamente archivos `.sql` en el sistema de ficheros. Por ello, el `STORED PROCEDURE` en la base de datos simplemente **registra su ejecución** en una tabla `backup_log` para fines de demostración y seguimiento interno.

La **generación real de archivos `.sql` de backup** se realiza a través de la funcionalidad de backup manual en la aplicación web. Esta funcionalidad utiliza `mysqldump` (con detección de sistema operativo para asegurar la portabilidad en XAMPP para Linux, macOS y Windows) para crear un archivo `.sql` completo de la base de datos. Este enfoque permite al usuario generar backups bajo demanda y gestionar los archivos resultantes (descargar, eliminar).

## Nota sobre la Gestión del Historial de Estancias (ALBIRAMENT)

El enunciado del proyecto requería un sistema automático (sugerido como un `TRIGGER`) para gestionar el cambio de colonia de un gato, actualizando su historial en la tabla `Estancia`.

**Decisión de Diseño Final:**

Durante la implementación, se detectó que el uso de un `TRIGGER` en la tabla `Estancia` (tanto `BEFORE INSERT` como `AFTER INSERT`) para actualizar la misma tabla `Estancia` provocaba un error irresoluble de MySQL:
> **Error 1442: Can't update table 'Estancia' in stored function/trigger because it is already used by statement which invoked this stored function/trigger.**

Este error es una limitación fundamental de MySQL para prevenir recursiones y bloqueos.

Para cumplir con el requisito funcional (mantener un historial correcto de estancias) y garantizar la estabilidad de la aplicación, se tomó la siguiente decisión:
1.  **Se eliminó el `TRIGGER`** de la base de datos.
2.  La lógica para finalizar la estancia antigua (`UPDATE`) y crear la nueva (`INSERT`) se ha implementado directamente en la aplicación PHP, dentro de una **transacción de base de datos** en el método `updateGato` del modelo `models/Gato.php`.

Este enfoque garantiza que la operación sea atómica (o todo se ejecuta o nada lo hace) y cumple con el comportamiento requerido en el enunciado, aunque la implementación se realice en la capa de aplicación en lugar de en la capa de base de datos para sortear una limitación técnica de MySQL.

## Sistema de Estado de Voluntarios

Se ha implementado un sistema de estado dinámico para los voluntarios que se muestra en la sección "Bolsín de Voluntarios". El estado de una persona se determina automáticamente en tiempo real según las siguientes reglas:

-   **Interesado**: Una persona que se ha registrado en el bolsín pero aún no ha sido aceptada como voluntario.
-   **Voluntario Inactivo**: Una persona que ha sido aceptada como voluntario pero que actualmente no está asignada a ningún grupo de trabajo.
-   **Voluntario Activo**: Un voluntario que está asignado a, al menos, un grupo de trabajo.

Este estado se actualiza visualmente de forma automática cada vez que se visita la página del bolsín, reflejando si un voluntario ha sido añadido o eliminado de un grupo.

## Refactorización de Incidencias

La sección de 'Incidencias' ha sido dividida en dos páginas para mayor claridad:

1.  **Incidencias Ocurridas**: Un listado histórico de todas las incidencias que han sido registradas durante las visitas. Permite ver qué pasó, cuándo y dónde.
2.  **Gestión de Tipos**: (Solo para Ayuntamientos) Un CRUD simple para administrar los *tipos* de incidencias que pueden ser seleccionados al registrar una nueva ocurrencia. Por ejemplo: 'Gato enfermo', 'Falta de comida', etc.

## Contacto

Para cualquier duda o problema, contacta con el desarrollador.