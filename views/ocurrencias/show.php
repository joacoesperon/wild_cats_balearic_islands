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
            <p><strong>Visita Asociada:</strong> <?php echo htmlspecialchars($incidencia->fechaVisita); ?> (Colonia: <?php echo htmlspecialchars($incidencia->colonia_nombre); ?>)</p>
            <p><strong>Gato Afectado:</strong>
                <?php if ($incidencia->idGato): ?>
                    <?php echo htmlspecialchars($incidencia->gato_nombre); ?>
                <?php else: ?>
                    N/A
                <?php endif; ?>
            </p>
            <p><strong>Ayuntamiento:</strong> <?php echo htmlspecialchars($incidencia->ayuntamiento_nombre); ?></p>
        </div>
    </div>

    <?php
    $from_visit_id = $_GET['from_visit'] ?? null;
    if ($from_visit_id) {
        $back_url = url('visitas/show?id=' . htmlspecialchars($from_visit_id));
        if (isset($_GET['from_profile'])) {
            $back_url .= '&from_profile=' . htmlspecialchars($_GET['from_profile']);
        }
        echo '<a href="' . $back_url . '" class="btn btn-secondary">Volver a la Visita</a>';
    } else {
        echo '<a href="' . url('ocurrencias') . '" class="btn btn-secondary">Volver al Listado</a>';
    }
    ?>

    <?php if ($_SESSION['user_type'] === 'ayuntamiento'): ?>
        <a href="<?php echo url('ocurrencias/edit?id=' . htmlspecialchars($incidencia->idIncidenciaVisita)); ?>" class="btn btn-warning">Editar Incidencia</a>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
