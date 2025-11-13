<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1><?php echo isset($incidencia) ? 'Editar Incidencia' : 'Registrar Nueva Incidencia'; ?></h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo url('incidencias/' . (isset($incidencia) ? 'update' : 'store')); ?>" method="POST">
        <?php if (isset($incidencia)): ?>
            <input type="hidden" name="idIncidencia" value="<?php echo htmlspecialchars($incidencia->idIncidencia); ?>">
        <?php endif; ?>

        <div class="mb-3">
            <label for="textoDescriptivo" class="form-label">Descripción de la Incidencia</label>
            <textarea class="form-control" id="textoDescriptivo" name="textoDescriptivo" rows="3" required><?php echo htmlspecialchars($incidencia->textoDescriptivo ?? ''); ?></textarea>
        </div>

        <div class="mb-3">
            <label for="idVisita" class="form-label">Visita Asociada</label>
            <select class="form-select" id="idVisita" name="idVisita" required>
                <option value="">-- Seleccionar Visita --</option>
                <?php foreach ($visitas as $visita): ?>
                    <option value="<?php echo htmlspecialchars($visita->idVisita); ?>" <?php echo (isset($incidencia) && $incidencia->idVisita == $visita->idVisita) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($visita->fechaVisita); ?> - <?php echo htmlspecialchars($visita->colonia_nombre); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="idGato" class="form-label">Gato Afectado (Opcional)</label>
            <select class="form-select" id="idGato" name="idGato">
                <option value="">-- Ninguno --</option>
                <?php foreach ($gatos as $gato): ?>
                    <option value="<?php echo htmlspecialchars($gato->idGato); ?>" <?php echo (isset($incidencia) && $incidencia->idGato == $gato->idGato) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($gato->nombre); ?> (Colonia: <?php echo htmlspecialchars($gato->colonia_actual ?? 'N/A'); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary"><?php echo isset($incidencia) ? 'Actualizar Incidencia' : 'Registrar Incidencia'; ?></button>
        <a href="<?php echo url('incidencias'); ?>" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>