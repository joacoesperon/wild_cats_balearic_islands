<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Gestión de Tareas (Trabajos)</h1>

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

    <?php if ($_SESSION['user_type'] === 'ayuntamiento'): ?>
    <a href="<?php echo url('trabajos/create'); ?>" class="btn btn-primary mb-3">Asignar Nueva Tarea</a>
    <?php endif; ?>

    <?php if (empty($trabajos)): ?>
        <div class="alert alert-info" role="alert">
            No hay tareas asignadas en este ayuntamiento.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Descripción</th>
                    <th>Grupo Asignado</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trabajos as $trabajo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($trabajo->descripcionTrabajo); ?></td>
                        <td><?php echo htmlspecialchars($trabajo->nombreGrupo); ?></td>
                        <td>
                            <?php if ($trabajo->completado): ?>
                                <span class="badge bg-success">Completada</span>
                            <?php else: ?>
                                <span class="badge bg-warning">Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($_SESSION['user_type'] === 'ayuntamiento'): ?>
                                <a href="<?php echo url('trabajos/edit?id=' . htmlspecialchars($trabajo->idTrabajo)); ?>" class="btn btn-warning btn-sm">Editar</a>
                                <form action="<?php echo url('trabajos/delete'); ?>" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="idTrabajo" value="<?php echo htmlspecialchars($trabajo->idTrabajo); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar esta tarea?');">Eliminar</button>
                                </form>
                            <?php else: ?>
                                <?php if (!$trabajo->completado): ?>
                                    <form action="<?php echo url('trabajos/completar'); ?>" method="POST" style="display:inline;">
                                        <input type="hidden" name="idTrabajo" value="<?php echo $trabajo->idTrabajo; ?>">
                                        <input type="hidden" name="return_to" value="list">
                                        <button type="submit" class="btn btn-primary btn-sm">Marcar como Completada</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">No disponible</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
