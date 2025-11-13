<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1><?php echo isset($grupo) ? 'Editar Grupo' : 'Añadir Nuevo Grupo'; ?></h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo url('grupos/' . (isset($grupo) ? 'update' : 'store')); ?>" method="POST">
        <?php if (isset($grupo)): ?>
            <input type="hidden" name="idGrupo" value="<?php echo htmlspecialchars($grupo->idGrupo); ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="nombreGrupo" class="form-label">Nombre del Grupo</label>
            <input type="text" class="form-control" id="nombreGrupo" name="nombreGrupo" value="<?php echo htmlspecialchars($grupo->nombreGrupo ?? ''); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary"><?php echo isset($grupo) ? 'Actualizar Grupo' : 'Guardar Grupo'; ?></button>
        <a href="<?php echo url('grupos'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>