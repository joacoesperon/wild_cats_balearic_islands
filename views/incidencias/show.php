<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Detalles de la Incidencia #<?php echo htmlspecialchars($incidencia->idIncidencia); ?></h1>

    <div class="card mb-3">
        <div class="card-header">
            Información de la Incidencia
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($incidencia->idIncidencia); ?></p>
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($incidencia->textoDescriptivo); ?></p>
            <p><strong>Visita Asociada:</strong> <a href="<?php echo url('visitas/show?id=' . htmlspecialchars($incidencia->idVisita)); ?>"><?php echo htmlspecialchars($incidencia->fechaVisita); ?> (Colonia: <?php echo htmlspecialchars($incidencia->colonia_nombre); ?>)</a></p>
            <p><strong>Gato Afectado:</strong>
                <?php if ($incidencia->idGato): ?>
                    <a href="<?php echo url('gatos/show?id=' . htmlspecialchars($incidencia->idGato)); ?>"><?php echo htmlspecialchars($incidencia->gato_nombre); ?></a>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </p>
            <p><strong>Ayuntamiento:</strong> <?php echo htmlspecialchars($incidencia->ayuntamiento_nombre); ?></p>
        </div>
    </div>

    <a href="<?php echo url('incidencias'); ?>" class="btn btn-secondary">Volver al Listado</a>
    <?php if ($_SESSION['user_type'] === 'ayuntamiento'): ?>
        <a href="<?php echo url('incidencias/edit?id=' . htmlspecialchars($incidencia->idIncidencia)); ?>" class="btn btn-warning">Editar Incidencia</a>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
