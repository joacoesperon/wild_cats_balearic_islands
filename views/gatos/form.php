<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1><?php echo isset($gato->idGato) ? 'Editar Gato' : 'Añadir Nuevo Gato'; ?></h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo url('gatos/' . (isset($gato->idGato) ? 'update' : 'store')); ?>" method="POST" enctype="multipart/form-data">
        <?php if (isset($gato->idGato)): ?>
            <input type="hidden" name="idGato" value="<?php echo htmlspecialchars($gato->idGato); ?>">
            <input type="hidden" name="oldIdColonia" value="<?php echo htmlspecialchars($gato->idColoniaActual ?? ''); ?>">
            <input type="hidden" name="fotoActual" value="<?php echo htmlspecialchars($gato->foto ?? ''); ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($gato->nombre ?? ''); ?>" required>
        </div>

        <div class="mb-3">
            <label for="descripcionAspecto" class="form-label">Descripción de Aspecto</label>
            <textarea class="form-control" id="descripcionAspecto" name="descripcionAspecto" rows="3"><?php echo htmlspecialchars($gato->descripcionAspecto ?? ''); ?></textarea>
        </div>

<div class="col-md-6">
                <label for="numeroChip" class="form-label">Número de Chip</label>
                <input type="text" class="form-control" id="numeroChip" name="numeroChip" value="<?php echo htmlspecialchars($gato->numeroChip ?? ''); ?>">
                <div class="form-text">El formato debe ser de 4 dígitos, ej: 1234.</div>
            </div>

        <div class="mb-3">
            <label for="idSexo" class="form-label">Sexo</label>
            <select class="form-select" id="idSexo" name="idSexo" required>
                <?php foreach ($sexos as $sex): ?>
                    <option value="<?php echo htmlspecialchars($sex->idSexo); ?>" <?php echo (isset($gato->idGato) && $gato->idSexo == $sex->idSexo) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($sex->sexo); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="idColonia" class="form-label">Colonia Actual</label>
            <select class="form-select" id="idColonia" name="idColonia">
                <option value="">-- Seleccionar Colonia --</option>
                <?php foreach ($colonias as $col): ?>
                    <option value="<?php echo htmlspecialchars($col->idColonia); ?>" <?php echo (isset($gato->idGato) && $gato->idColoniaActual == $col->idColonia) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($col->descripcion); ?> (<?php echo htmlspecialchars($col->ayuntamiento_nombre); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="foto" class="form-label">Foto del Gato</label>
            <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
            <?php if (isset($gato->idGato) && $gato->foto): ?>
                <div class="mt-2">
                    <p>Foto actual:</p>
                    <img src="<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/public/uploads/gatos/<?php echo htmlspecialchars($gato->foto); ?>" alt="Foto actual" class="gato-img-thumbnail">
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary"><?php echo isset($gato->idGato) ? 'Actualizar Gato' : 'Guardar Gato'; ?></button>
        <a href="<?php echo url('gatos'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>