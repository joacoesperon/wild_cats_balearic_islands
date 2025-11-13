<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1><?php echo isset($colonia) ? 'Editar Colonia' : 'Añadir Nueva Colonia'; ?></h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo url('colonias/' . (isset($colonia) ? 'update' : 'store')); ?>" method="POST">
        <?php if (isset($colonia)): ?>
            <input type="hidden" name="idColonia" value="<?php echo htmlspecialchars($colonia->idColonia); ?>">
            <input type="hidden" name="idUbicacion" value="<?php echo htmlspecialchars($colonia->idUbicacion); ?>">
            <input type="hidden" name="idCoordenada" value="<?php echo htmlspecialchars($colonia->idCoordenada); ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción de la Colonia</label>
            <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($colonia->descripcion ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label for="comentarios" class="form-label">Comentarios</label>
            <textarea class="form-control" id="comentarios" name="comentarios" rows="3"><?php echo htmlspecialchars($colonia->comentarios ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="ubicacion_descripcion" class="form-label">Descripción de la Ubicación</label>
            <input type="text" class="form-control" id="ubicacion_descripcion" name="ubicacion_descripcion" value="<?php echo htmlspecialchars($colonia->ubicacion_descripcion ?? ''); ?>" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="latitud" class="form-label">Latitud</label>
                <input type="number" step="0.000001" class="form-control" id="latitud" name="latitud" value="<?php echo htmlspecialchars($colonia->latitud ?? ''); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="longitud" class="form-label">Longitud</label>
                <input type="number" step="0.000001" class="form-control" id="longitud" name="longitud" value="<?php echo htmlspecialchars($colonia->longitud ?? ''); ?>" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><?php echo isset($colonia) ? 'Actualizar Colonia' : 'Guardar Colonia'; ?></button>
        <a href="<?php echo url('colonias'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>