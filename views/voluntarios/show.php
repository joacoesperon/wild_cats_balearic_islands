<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Mi Perfil de Voluntario</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <div class="card mb-3">
        <div class="card-header">
            Información de Voluntario
        </div>
        <div class="card-body">
            <p><strong>Usuario:</strong> <?php echo htmlspecialchars($voluntario->usuario); ?></p>
            <p><strong>Nombre Completo:</strong> <?php echo htmlspecialchars($interesado->nombreCompleto); ?></p>
            <p><strong>DNI:</strong> <?php echo htmlspecialchars($interesado->DNI); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($interesado->email); ?></p>
            <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($interesado->telefono ?? 'N/A'); ?></p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Mis Grupos
        </div>
        <div class="card-body">
            <?php if (empty($grupos)): ?>
                <p>No perteneces a ningún grupo actualmente.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($grupos as $grupo): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <?php echo htmlspecialchars($grupo->nombreGrupo); ?>
                                <?php if ($grupo->es_responsable): ?>
                                    <span class="badge bg-success">Responsable</span>
                                <?php endif; ?>
                            </div>
                            <a href="<?php echo url('grupos/show?id=' . htmlspecialchars($grupo->idGrupo)); ?>" class="btn btn-info btn-sm">Ver</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Mis Tareas / Visitas Asignadas
        </div>
        <div class="card-body">
            <h5>Tareas Asignadas</h5>
            <?php if (empty($tareas)): ?>
                <p>No tienes tareas asignadas.</p>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Grupo</th>
                            <th>Descripción de la Tarea</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tareas as $tarea): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($tarea->nombreGrupo); ?></td>
                                <td><?php echo htmlspecialchars($tarea->descripcionTrabajo); ?></td>
                                <td>
                                    <?php if ($tarea->completado): ?>
                                        <span class="badge bg-success">Completada</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Pendiente</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!$tarea->completado): ?>
                                        <form action="<?php echo url('trabajos/completar'); ?>" method="POST" style="display:inline;">
                                            <input type="hidden" name="idTrabajo" value="<?php echo $tarea->idTrabajo; ?>">
                                            <button type="submit" class="btn btn-primary btn-sm">Marcar como Completada</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

            <h5 class="mt-4">Visitas</h5>
            <?php if (empty($visitas)): ?>
                <p>No tienes visitas programadas.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($visitas as $visita): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                Visita a la colonia "<?php echo htmlspecialchars($visita->nombreColonia); ?>"
                                <span class="badge bg-info rounded-pill"><?php echo date('d/m/Y', strtotime($visita->fechaVisita)); ?></span>
                            </div>
                            <a href="<?php echo url('visitas/show?id=' . htmlspecialchars($visita->idVisita) . '&from_profile=' . htmlspecialchars($_SESSION['user_id'])); ?>" class="btn btn-info btn-sm">Ver</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <a href="<?php echo url(''); ?>" class="btn btn-secondary">Volver al Inicio</a>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>