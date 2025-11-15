<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1><?php echo isset($tipo) ? 'Editar Tipo de Incidencia' : 'Crear Nuevo Tipo de Incidencia'; ?></h1>

    <form action="<?php echo url('tipos_incidencia/' . (isset($tipo) ? 'update' : 'store')); ?>" method="POST">
        <?php if (isset($tipo)): ?>
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($tipo->idIncidencia); ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="textoDescriptivo" class="form-label">Descripción</label>
            <input type="text" class="form-control" id="textoDescriptivo" name="textoDescriptivo" value="<?php echo htmlspecialchars($tipo->textoDescriptivo ?? ''); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary"><?php echo isset($tipo) ? 'Actualizar' : 'Crear'; ?></button>
        <a href="<?php echo url('tipos_incidencia'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
