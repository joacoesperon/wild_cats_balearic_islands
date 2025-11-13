<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1><?php echo isset($interesado) ? 'Editar Interesado' : 'Registrar Nuevo Interesado'; ?></h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo url('interesados/' . (isset($interesado) ? 'update' : 'store')); ?>" method="POST">
        <?php if (isset($interesado)): ?>
            <input type="hidden" name="idInteresado" value="<?php echo htmlspecialchars($interesado->idInteresado); ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="DNI" class="form-label">DNI</label>
            <input type="text" class="form-control" id="DNI" name="DNI" value="<?php echo htmlspecialchars($interesado->DNI ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label for="nombreCompleto" class="form-label">Nombre Completo</label>
            <input type="text" class="form-control" id="nombreCompleto" name="nombreCompleto" value="<?php echo htmlspecialchars($interesado->nombreCompleto ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($interesado->email ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($interesado->telefono ?? ''); ?>">
        </div>

        <button type="submit" class="btn btn-primary"><?php echo isset($interesado) ? 'Actualizar Interesado' : 'Registrar Interesado'; ?></button>
        <a href="<?php echo url('interesados'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
