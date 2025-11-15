<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1><?php echo isset($visita) ? 'Editar Visita' : 'Registrar Nueva Visita'; ?></h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo url('visitas/' . (isset($visita) ? 'update' : 'store')); ?>" method="POST">
        <?php if (isset($visita)): ?>
            <input type="hidden" name="idVisita" value="<?php echo htmlspecialchars($visita->idVisita); ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="fechaVisita" class="form-label">Fecha de la Visita</label>
            <input type="date" class="form-control" id="fechaVisita" name="fechaVisita" value="<?php echo htmlspecialchars($visita->fechaVisita ?? date('Y-m-d')); ?>" required>
        </div>

        <div class="mb-3">
            <label for="idColonia" class="form-label">Colonia Visitada</label>
            <select class="form-select" id="idColonia" name="idColonia" required>
                <option value="">-- Seleccionar Colonia --</option>
                <?php foreach ($colonias as $colonia): ?>
                    <option value="<?php echo htmlspecialchars($colonia->idColonia); ?>" <?php echo (isset($visita) && $visita->idColonia == $colonia->idColonia) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($colonia->descripcion); ?> (<?php echo htmlspecialchars($colonia->ayuntamiento_nombre); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="voluntarios" class="form-label">Voluntarios Participantes</label>
            <select class="form-select" id="voluntarios" name="voluntarios[]" multiple>
                <?php foreach ($voluntarios as $voluntario): ?>
                    <option value="<?php echo htmlspecialchars($voluntario->idVoluntario); ?>"
                        <?php echo (isset($voluntariosSeleccionados) && in_array($voluntario->idVoluntario, $voluntariosSeleccionados)) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($voluntario->usuario); ?> (<?php echo htmlspecialchars($voluntario->nombreCompleto); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="form-text">Mantén pulsado Ctrl (Windows) o Cmd (Mac) para seleccionar múltiples voluntarios.</div>
        </div>

        <!-- Sección de Incidencias Dinámicas -->
        <?php if (!isset($visita)): // Solo mostrar en la creación ?>
            <hr>
            <h4>Incidencias de la Visita</h4>
            <div id="incidencias-container">
                <!-- Las filas de incidencias se añadirán aquí -->
            </div>
            <button type="button" id="add-incidencia-btn" class="btn btn-outline-success btn-sm mt-2">+ Añadir Incidencia</button>
        <?php endif; ?>
        <!-- Fin de Sección de Incidencias -->

        <hr>
        <button type="submit" class="btn btn-primary"><?php echo isset($visita) ? 'Actualizar Visita' : 'Registrar Visita'; ?></button>
        <a href="<?php echo url('visitas'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<!-- Plantilla para una nueva fila de incidencia (oculta) -->
<template id="incidencia-template">
    <div class="row mb-2 align-items-center incidencia-row">
        <div class="col-md-5">
            <select class="form-select" name="incidencia_tipos[]" required>
                <option value="">-- Seleccionar Tipo de Incidencia --</option>
                <?php foreach ($tipos_incidencia as $tipo): ?>
                    <option value="<?php echo htmlspecialchars($tipo->idIncidencia); ?>"><?php echo htmlspecialchars($tipo->textoDescriptivo); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5">
            <select class="form-select" name="incidencia_gatos[]">
                <option value="">-- Gato Afectado (Opcional) --</option>
                <?php foreach ($gatos as $gato): ?>
                    <option value="<?php echo htmlspecialchars($gato->idGato); ?>"><?php echo htmlspecialchars($gato->nombre); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm remove-incidencia-btn">Eliminar</button>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Solo ejecutar si estamos en el formulario de creación
    const addBtn = document.getElementById('add-incidencia-btn');
    if (!addBtn) return;

    const container = document.getElementById('incidencias-container');
    const template = document.getElementById('incidencia-template');
    const coloniaSelect = document.getElementById('idColonia');

    // --- Lógica para añadir y quitar filas ---
    addBtn.addEventListener('click', function () {
        const clone = template.content.cloneNode(true);
        container.appendChild(clone);
    });

    container.addEventListener('click', function (e) {
        if (e.target && e.target.classList.contains('remove-incidencia-btn')) {
            e.target.closest('.incidencia-row').remove();
        }
    });

    // --- Lógica para actualizar gatos según la colonia seleccionada ---
    const updateCatDropdowns = (gatos) => {
        // Actualizar el desplegable de gatos en la plantilla
        const templateGatoDropdown = template.content.querySelector('select[name="incidencia_gatos[]"]');
        updateGatoDropdownOptions(templateGatoDropdown, gatos);

        // Actualizar los desplegables de gatos en las filas ya existentes
        const existingRows = container.querySelectorAll('.incidencia-row');
        existingRows.forEach(row => {
            const gatoDropdown = row.querySelector('select[name="incidencia_gatos[]"]');
            updateGatoDropdownOptions(gatoDropdown, gatos);
        });
    };

    const updateGatoDropdownOptions = (dropdown, gatos) => {
        // Guardar el valor seleccionado actual si existe
        const selectedValue = dropdown.value;

        // Limpiar opciones existentes (excepto la primera, que es el placeholder)
        while (dropdown.options.length > 1) {
            dropdown.remove(1);
        }
        // Añadir nuevas opciones
        gatos.forEach(gato => {
            const option = new Option(gato.nombre, gato.idGato);
            dropdown.add(option);
        });

        // Restaurar el valor seleccionado si todavía existe en las nuevas opciones
        if (selectedValue && Array.from(dropdown.options).some(option => option.value === selectedValue)) {
            dropdown.value = selectedValue;
        }
    };

    coloniaSelect.addEventListener('change', function() {
        const idColonia = this.value;
        const url = '<?php echo url("gatos/ajax_get_by_colonia"); ?>' + '?idColonia=' + idColonia;

        if (idColonia) {
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta de la red');
                    }
                    return response.json();
                })
                .then(gatos => {
                    updateCatDropdowns(gatos);
                })
                .catch(error => {
                    console.error('Error al cargar los gatos:', error);
                    // Opcional: mostrar un error al usuario
                });
        } else {
            // Si no hay colonia seleccionada, vaciar los desplegables
            updateCatDropdowns([]);
        }
    });

    // Disparar el evento 'change' al cargar la página si ya hay una colonia seleccionada
    if (coloniaSelect.value) {
        coloniaSelect.dispatchEvent(new Event('change'));
    }
});
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>