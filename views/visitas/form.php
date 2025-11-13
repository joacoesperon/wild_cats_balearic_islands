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

        <button type="submit" class="btn btn-primary"><?php echo isset($visita) ? 'Actualizar Visita' : 'Registrar Visita'; ?></button>
        <a href="<?php echo url('visitas'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>