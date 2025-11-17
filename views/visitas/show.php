<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Detalles de la Visita #<?php echo htmlspecialchars($visita->idVisita); ?></h1>

    <div class="card mb-3">
        <div class="card-header">
            Información de la Visita
        </div>
        <div class="card-body">
            <p><strong>Fecha de Visita:</strong> <?php echo htmlspecialchars($visita->fechaVisita); ?></p>
            <p><strong>Colonia:</strong> <?php echo htmlspecialchars($visita->colonia_nombre); ?></p>
            <p><strong>Ayuntamiento:</strong> <?php echo htmlspecialchars($visita->ayuntamiento_nombre); ?></p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Voluntarios Participantes
        </div>
        <div class="card-body">
            <?php if (empty($voluntariosParticipantes)): ?>
                <p>No hay voluntarios registrados para esta visita.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($voluntariosParticipantes as $voluntario): ?>
                        <li><?php echo htmlspecialchars($voluntario->voluntario_nombre); ?> (<?php echo htmlspecialchars($voluntario->usuario); ?>)</li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            Incidencias Registradas en esta Visita
        </div>
        <div class="card-body">
            <?php if (empty($incidencias)): ?>
                <p>No hay incidencias registradas para esta visita.</p>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Gato Afectado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($incidencias as $incidencia): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($incidencia->textoDescriptivo); ?></td>
                                <td>
                                    <?php if ($incidencia->idGato): ?>
                                        <?php echo htmlspecialchars($incidencia->gato_nombre ?: 'Gato no encontrado'); ?>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $ocurrencia_url = url('ocurrencias/show?id=' . htmlspecialchars($incidencia->idIncidenciaVisita) . '&from_visit=' . htmlspecialchars($visita->idVisita));
                                    if (isset($_GET['from_profile'])) {
                                        $ocurrencia_url .= '&from_profile=' . htmlspecialchars($_GET['from_profile']);
                                    }
                                    ?>
                                    <a href="<?php echo $ocurrencia_url; ?>" class="btn btn-info btn-sm">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <?php
    $from_profile_id = $_GET['from_profile'] ?? null;
    if ($from_profile_id):
    ?>
        <a href="<?php echo url('voluntarios/show?id=' . htmlspecialchars($from_profile_id)); ?>" class="btn btn-secondary">Volver a Mi Perfil</a>
    <?php else: ?>
        <a href="<?php echo url('visitas'); ?>" class="btn btn-secondary">Volver al Listado</a>
    <?php endif; ?>

    <?php if ($_SESSION['user_type'] === 'ayuntamiento'): ?>
        <a href="<?php echo url('visitas/edit?id=' . htmlspecialchars($visita->idVisita)); ?>" class="btn btn-warning">Editar Visita</a>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>