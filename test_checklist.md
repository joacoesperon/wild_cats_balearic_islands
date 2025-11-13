# Lista de Verificación de Pruebas para la Aplicación de Gestión de Colonias Felinas

Este documento detalla los pasos para verificar la correcta funcionalidad de la aplicación.

## 1. Configuración Inicial y Base de Datos

-   [ ] **Verificar instalación XAMPP**: Asegurarse de que Apache y MySQL estén corriendo.
-   [ ] **Importar `gatos_mallorca.sql`**:
    -   [ ] Acceder a `http://localhost/phpmyadmin/`.
    -   [ ] Importar el archivo `sql/gatos_mallorca.sql`.
    -   [ ] **Verificar base de datos `gatos_mallorca`**: Confirmar que la base de datos y todas las tablas (Ayuntamiento, Colonia, Gato, Grupo, Incidencia, Interesado, Pertenencia, Provincia, Sexo, Ubicacion, Coordenada, Estancia, Visita, Voluntario, Trabajo, BolsaMunicipal, InteresadoBolsa, VisitaVoluntario, backup_log) han sido creadas.
    -   [ ] **Verificar datos de prueba**: Confirmar que las tablas contienen los datos de prueba insertados (3 Ayuntamientos, 6 Colonias, 20 Gatos, 10 Voluntarios, 2 Grupos, etc.).
    -   [ ] **Verificar `STORED PROCEDURE`**: Confirmar la existencia del `sp_backup_gatos_mallorca`.
    -   [ ] **Verificar `EVENT`**: Confirmar la existencia del `daily_backup_event` y que el `event_scheduler` está `ON`.
    -   [ ] **Verificar `TRIGGER`**: Confirmar la existencia del `trg_after_insert_estancia` en la tabla `Estancia`.

## 2. Acceso a la Aplicación y Autenticación

-   [ ] **Acceder a la URL base**: Abrir `http://localhost/practica2/` en el navegador.
    -   [ ] **Redirección a Login**: Verificar que se redirige automáticamente a la página de login.
-   [ ] **Login de Ayuntamiento**:
    -   [ ] Intentar login con credenciales incorrectas (ej. `palma_user`, `wrong_pass`). Verificar mensaje de error.
    -   [ ] Intentar login con credenciales correctas (ej. `palma_user`, `palma123`).
    -   [ ] **Verificar acceso**: Confirmar que se accede al dashboard o página principal del ayuntamiento.
    -   [ ] **Verificar sesión**: Cerrar el navegador y volver a abrirlo. Verificar que la sesión se mantiene (o que se requiere login de nuevo, según la implementación de sesión).
-   [ ] **Login de Voluntario**:
    -   [ ] Intentar login con credenciales incorrectas (ej. `jperez`, `wrong_pass`). Verificar mensaje de error.
    -   [ ] Intentar login con credenciales correctas (ej. `jperez`, `pass123`).
    -   [ ] **Verificar acceso**: Confirmar que se accede al dashboard o página principal del voluntario.
-   [ ] **Logout**:
    -   [ ] Hacer clic en el botón/enlace de "Cerrar Sesión".
    -   [ ] **Verificar redirección**: Confirmar que se redirige a la página de login y la sesión se ha cerrado.

## 3. Funcionalidades CRUD (Ayuntamiento Logueado)

Acceder como `palma_user` (`palma123`) para realizar las siguientes pruebas.

### 3.1. Gestión de Colonias

-   [ ] **Listar Colonias**:
    -   [ ] Navegar a la sección de "Colonias" (o desde el dashboard).
    -   [ ] **Verificar listado**: Confirmar que se muestran las colonias asociadas al ayuntamiento de Palma.
-   [ ] **Crear Nueva Colonia**:
    -   [ ] Hacer clic en "Añadir Nueva Colonia".
    -   [ ] Rellenar el formulario con datos válidos (descripción, comentarios, latitud, longitud).
    -   [ ] **Verificar creación**: Confirmar que la nueva colonia aparece en el listado y los datos son correctos.
-   [ ] **Ver Detalles de Colonia**:
    -   [ ] Hacer clic en "Ver" o en el nombre de una colonia.
    -   [ ] **Verificar detalles**: Confirmar que se muestran todos los datos de la colonia, incluyendo ubicación y gatos asociados.
-   [ ] **Editar Colonia**:
    -   [ ] Hacer clic en "Editar" en una colonia existente.
    -   [ ] Modificar algunos campos (ej. comentarios, latitud).
    -   [ ] **Verificar actualización**: Confirmar que los cambios se reflejan en el listado y en la vista de detalles.
-   [ ] **Eliminar Colonia**:
    -   [ ] Hacer clic en "Eliminar" en una colonia de prueba (sin datos asociados si es posible).
    -   [ ] **Verificar eliminación**: Confirmar que la colonia desaparece del listado.

### 3.2. Gestión de Gatos

-   [ ] **Listar Gatos**:
    -   [ ] Navegar a la sección de "Gatos" (o desde el dashboard).
    -   [ ] **Verificar listado**: Confirmar que se muestran los gatos.
-   [ ] **Crear Nuevo Gato**:
    -   [ ] Hacer clic en "Añadir Nuevo Gato".
    -   [ ] Rellenar el formulario con datos válidos (nombre, descripción, chip, sexo, colonia actual).
    -   [ ] **Subir foto**: Seleccionar un archivo de imagen.
    -   [ ] **Verificar creación**: Confirmar que el nuevo gato aparece en el listado, los datos son correctos y la foto se ha subido a `public/uploads/gatos/`.
-   [ ] **Ver Detalles de Gato**:
    -   [ ] Hacer clic en "Ver" o en el nombre de un gato.
    -   [ ] **Verificar detalles**: Confirmar que se muestran todos los datos del gato, incluyendo su historial de estancias.
-   [ ] **Editar Gato**:
    -   [ ] Hacer clic en "Editar" en un gato existente.
    -   [ ] Modificar algunos campos (ej. nombre, descripción).
    -   [ ] **Cambiar Colonia**: Modificar la colonia asignada al gato.
    -   [ ] **Verificar actualización**: Confirmar que los cambios se reflejan.
-   [ ] **Eliminar Gato**:
    -   [ ] Hacer clic en "Eliminar" en un gato de prueba.
    -   [ ] **Verificar eliminación**: Confirmar que el gato desaparece del listado.

### 3.3. Gestión de Grupos

-   [ ] **Listar Grupos**:
    -   [ ] Navegar a la sección de "Grupos" (o desde el dashboard).
    -   [ ] **Verificar listado**: Confirmar que se muestran los grupos asociados al ayuntamiento.
-   [ ] **Crear Nuevo Grupo**:
    -   [ ] Hacer clic en "Añadir Nuevo Grupo".
    -   [ ] Rellenar el formulario con un nombre de grupo.
    -   [ ] **Verificar creación**: Confirmar que el nuevo grupo aparece en el listado.
-   [ ] **Ver Detalles de Grupo**:
    -   [ ] Hacer clic en "Ver" o en el nombre de un grupo.
    -   [ ] **Verificar detalles**: Confirmar que se muestran los miembros del grupo y si son responsables.
-   [ ] **Editar Grupo**:
    -   [ ] Hacer clic en "Editar" en un grupo existente.
    -   [ ] Modificar el nombre del grupo.
    -   [ ] **Gestionar miembros**: Añadir/eliminar voluntarios al grupo, cambiar rol de responsable.
    -   [ ] **Verificar actualización**: Confirmar que los cambios se reflejan.
-   [ ] **Eliminar Grupo**:
    -   [ ] Hacer clic en "Eliminar" en un grupo de prueba (sin voluntarios asociados si es posible).
    -   [ ] **Verificar eliminación**: Confirmar que el grupo desaparece del listado.

### 3.4. Gestión de Voluntarios (Bolsín)

-   [ ] **Listar Interesados (Bolsín)**:
    -   [ ] Navegar a la sección de "Bolsín Voluntarios" (o desde el dashboard).
    -   [ ] **Verificar listado**: Confirmar que se muestran los interesados registrados.
-   [ ] **Registrar Nuevo Interesado**:
    -   [ ] Hacer clic en "Registrar Interesado".
    -   [ ] Rellenar el formulario con datos válidos (DNI, nombre, email, teléfono).
    -   [ ] **Verificar registro**: Confirmar que el nuevo interesado aparece en el listado.
-   [ ] **Convertir Interesado en Voluntario**:
    -   [ ] Desde el listado de interesados, seleccionar uno y "Aceptar como Voluntario".
    -   [ ] Asignar un usuario y contraseña.
    -   [ ] **Verificar creación de Voluntario**: Confirmar que el interesado se convierte en voluntario y puede loguearse.
-   [ ] **Editar Interesado/Voluntario**:
    -   [ ] Modificar datos de un interesado o voluntario.
    -   [ ] **Verificar actualización**.
-   [ ] **Eliminar Interesado/Voluntario**:
    -   [ ] Eliminar un interesado o voluntario de prueba.
    -   [ ] **Verificar eliminación**.

### 3.5. Gestión de Visitas

-   [ ] **Listar Visitas**:
    -   [ ] Navegar a la sección de "Visitas" (o desde el dashboard).
    -   [ ] **Verificar listado**: Confirmar que se muestran las visitas realizadas.
-   [ ] **Registrar Nueva Visita**:
    -   [ ] Hacer clic en "Registrar Visita".
    -   [ ] Rellenar el formulario (fecha, colonia, voluntarios participantes).
    -   [ ] **Verificar registro**: Confirmar que la nueva visita aparece en el listado.
-   [ ] **Ver Detalles de Visita**:
    -   [ ] Hacer clic en "Ver" en una visita.
    -   [ ] **Verificar detalles**: Confirmar que se muestran los datos de la visita y los voluntarios que participaron.
-   [ ] **Editar Visita**:
    -   [ ] Modificar datos de una visita.
    -   [ ] **Verificar actualización**.
-   [ ] **Eliminar Visita**:
    -   [ ] Eliminar una visita de prueba.
    -   [ ] **Verificar eliminación**.

### 3.6. Gestión de Incidencias

-   [ ] **Listar Incidencias**:
    -   [ ] Navegar a la sección de "Incidencias" (o desde el dashboard).
    -   [ ] **Verificar listado**: Confirmar que se muestran las incidencias registradas.
-   [ ] **Registrar Nueva Incidencia**:
    -   [ ] Hacer clic en "Registrar Incidencia".
    -   [ ] Rellenar el formulario (descripción, visita asociada, gato afectado si aplica).
    -   [ ] **Verificar registro**: Confirmar que la nueva incidencia aparece en el listado.
-   [ ] **Ver Detalles de Incidencia**:
    -   [ ] Hacer clic en "Ver" en una incidencia.
    -   [ ] **Verificar detalles**: Confirmar que se muestran los datos de la incidencia, la visita y el gato asociado.
-   [ ] **Editar Incidencia**:
    -   [ ] Modificar datos de una incidencia.
    -   [ ] **Verificar actualización**.
-   [ ] **Eliminar Incidencia**:
    -   [ ] Eliminar una incidencia de prueba.
    -   [ ] **Verificar eliminación**.

### 3.7. Gestión de Tareas (Trabajos)

-   [ ] **Listar Tareas**:
    -   [ ] Navegar a la sección de "Tareas" (o desde el dashboard).
    -   [ ] **Verificar listado**: Confirmar que se muestran las tareas asignadas a los grupos del ayuntamiento.
-   [ ] **Crear Nueva Tarea**:
    -   [ ] Hacer clic en "Asignar Nueva Tarea".
    -   [ ] Rellenar el formulario con descripción y seleccionar un grupo.
    -   [ ] **Verificar creación**: Confirmar que la nueva tarea aparece en el listado como "Pendiente".
-   [ ] **Editar Tarea**:
    -   [ ] Hacer clic en "Editar" en una tarea existente.
    -   [ ] Modificar descripción, grupo o cambiar el estado a "Completada" / "Pendiente".
    -   [ ] **Verificar actualización**: Confirmar que los cambios se reflejan en el listado.
-   [ ] **Eliminar Tarea**:
    -   [ ] Hacer clic en "Eliminar" en una tarea de prueba.
    -   [ ] **Verificar eliminación**: Confirmar que la tarea desaparece del listado.

## 4. Funcionalidades de Voluntario Logueado

Acceder como `jperez` (`pass123`) para realizar las siguientes pruebas.

-   [ ] **Ver Perfil**:
    -   [ ] Acceder a "Mi Perfil".
    -   [ ] **Verificar información personal**: Confirmar que se muestran los datos del voluntario.
    -   [ ] **Verificar Mis Grupos**: Confirmar que se listan los grupos a los que pertenece, indicando si es responsable.
    -   [ ] **Verificar Mis Tareas Asignadas**: Confirmar que se listan las tareas asignadas a sus grupos.
    -   [ ] **Verificar Mis Visitas Programadas**: Confirmar que se listan las visitas en las que participa.
-   [ ] **Marcar Tarea como Completada**:
    -   [ ] En la sección "Mis Tareas Asignadas", seleccionar una tarea "Pendiente".
    -   [ ] Hacer clic en "Marcar como Completada".
    -   [ ] **Verificar actualización**: Confirmar que la tarea cambia a estado "Completada" en el listado del voluntario.
    -   [ ] **Verificar en Ayuntamiento**: Loguearse como ayuntamiento y verificar que la tarea también aparece como "Completada" en su listado de tareas.

## 5. Funcionalidades de Backup

-   [ ] **Acceder a la sección de Backups**:
    -   [ ] Navegar a la sección de "Backups" (o desde el dashboard).
    -   [ ] **Verificar listado**: Confirmar que se muestra un listado de backups existentes.
-   [ ] **Generar Backup Manual**:
    -   [ ] Hacer clic en "Generar Backup Ahora".
    -   [ ] **Verificar creación**: Confirmar que aparece un nuevo archivo `backup_gatos_YYYYMMDD.sql` en el listado y en la carpeta `backups/`.
    -   [ ] **Verificar `backup_log`**: Comprobar que se ha insertado un nuevo registro en la tabla `backup_log` indicando la ejecución del SP.
-   [ ] **Descargar Backup**:
    -   [ ] Hacer clic en "Descargar" en un backup existente.
    -   [ ] **Verificar descarga**: Confirmar que el archivo `.sql` se descarga correctamente.
-   [ ] **Eliminar Backup**:
    -   [ ] Hacer clic en "Eliminar" en un backup de prueba.
    -   [ ] **Verificar eliminación**: Confirmar que el archivo desaparece del listado y de la carpeta `backups/`.

## 6. Funcionalidad del TRIGGER (ALBIRAMENT)

-   [ ] **Preparación**:
    -   [ ] Identificar un gato existente (ej. `Luna`, `idGato=1`) y su colonia actual (ej. `Colonia A`, `idColonia=1`).
    -   [ ] Identificar otra colonia (ej. `Colonia B`, `idColonia=2`).
-   [ ] **Cambiar Colonia del Gato**:
    -   [ ] Loguearse como ayuntamiento.
    -   [ ] Navegar a "Gatos" y editar el gato seleccionado.
    -   [ ] Cambiar su "Colonia Actual" a la nueva colonia (ej. `Colonia B`).
    -   [ ] Guardar cambios.
-   [ ] **Verificar `Estancia` anterior**:
    -   [ ] Acceder a phpMyAdmin y consultar la tabla `Estancia`.
    -   [ ] **Confirmar `fechaFin`**: Verificar que la `Estancia` anterior del gato (en `Colonia A`) ahora tiene su `fechaFin` establecida a la fecha del cambio.
-   [ ] **Verificar nueva `Estancia`**:
    -   [ ] Confirmar que se ha creado una nueva `Estancia` para el gato en la nueva colonia (`Colonia B`) con `fechaInicio` igual a la fecha del cambio y `fechaFin` como `NULL`.

## 7. Portabilidad y Estructura de Archivos

-   [ ] **Verificar creación de carpetas**:
    -   [ ] Asegurarse de que `public/uploads/gatos/` existe.
    -   [ ] Asegurarse de que `backups/` existe.
-   [ ] **Rutas relativas**:
    -   [ ] Inspeccionar el código para confirmar que no se usan rutas absolutas (excepto para la detección de `mysqldump` que es dinámica).
-   [ ] **Conexión a BD en localhost**:
    -   [ ] Verificar que `config/database.php` se conecta correctamente usando `root` sin contraseña cuando está en `localhost`.

## 8. Código y Documentación

-   [ ] **Comentarios en código PHP**:
    -   [ ] Verificar que el código PHP está bien comentado, explicando la lógica compleja.
-   [ ] **Comentarios en código SQL**:
    -   [ ] Verificar que `gatos_mallorca.sql` tiene comentarios claros.
-   [ ] **`README.md`**:
    -   [ ] Confirmar que el `README.md` es completo y claro.
-   [ ] **`test_checklist.md`**:
    -   [ ] Confirmar que este documento es útil y cubre los puntos clave.