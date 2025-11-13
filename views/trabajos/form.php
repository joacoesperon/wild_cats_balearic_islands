<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1><?php echo isset($trabajo->idTrabajo) ? 'Editar' : 'Asignar'; ?> Tarea</h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo isset($trabajo->idTrabajo) ? url('trabajos/update') : url('trabajos/store'); ?>" method="POST">
        <?php if (isset($trabajo->idTrabajo)): ?>
            <input type="hidden" name="idTrabajo" value="<?php echo htmlspecialchars($trabajo->idTrabajo); ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="descripcionTrabajo" class="form-label">Descripción de la Tarea</label>
            <textarea class="form-control" id="descripcionTrabajo" name="descripcionTrabajo" rows="3" required><?php echo htmlspecialchars($trabajo->descripcionTrabajo ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="idGrupo" class="form-label">Asignar al Grupo</label>
            <select class="form-select" id="idGrupo" name="idGrupo" required>
                <option value="">Seleccione un grupo</option>
                <?php foreach ($grupos as $grupo): ?>
                    <option value="<?php echo htmlspecialchars($grupo->idGrupo); ?>" <?php echo (isset($trabajo->idGrupo) && $trabajo->idGrupo == $grupo->idGrupo) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($grupo->nombreGrupo); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <?php if (isset($trabajo->idTrabajo)): ?>
            <div class="mb-3 form-check">
                <input type="hidden" name="completado" value="0"> <!-- Valor por defecto si el checkbox no se marca -->
                <input type="checkbox" class="form-check-input" id="completado" name="completado" value="1" <?php echo (isset($trabajo->completado) && $trabajo->completado) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="completado">Completada</label>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn btn-primary"><?php echo isset($trabajo->idTrabajo) ? 'Actualizar' : 'Guardar'; ?> Tarea</button>
        <a href="<?php echo url('trabajos'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
