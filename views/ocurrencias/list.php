<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Incidencias Ocurridas</h1>

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

    <?php /* Botón "Registrar Nueva Incidencia" eliminado según la nueva lógica de creación a través de Visitas */ ?>

    <?php if (empty($incidencias)): ?>
        <div class="alert alert-info" role="alert">
            No hay incidencias registradas.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Fecha Visita</th>
                    <th>Colonia</th>
                    <th>Gato Afectado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($incidencias as $incidencia): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($incidencia->idIncidenciaVisita); ?></td>
                        <td><?php echo htmlspecialchars($incidencia->textoDescriptivo); ?></td>
                        <td><?php echo htmlspecialchars($incidencia->fechaVisita); ?></td>
                        <td><?php echo htmlspecialchars($incidencia->colonia_nombre); ?></td>
                        <td><?php echo htmlspecialchars($incidencia->gato_nombre ?? 'N/A'); ?></td>
                        <td>
                            <a href="<?php echo url('ocurrencias/show?id=' . htmlspecialchars($incidencia->idIncidenciaVisita)); ?>" class="btn btn-info btn-sm">Ver</a>
                            <?php if ($_SESSION['user_type'] === 'ayuntamiento'): ?>
                                <a href="<?php echo url('ocurrencias/edit?id=' . htmlspecialchars($incidencia->idIncidenciaVisita)); ?>" class="btn btn-warning btn-sm">Editar</a>
                                <form action="<?php echo url('ocurrencias/delete'); ?>" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="idIncidencia" value="<?php echo htmlspecialchars($incidencia->idIncidenciaVisita); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar esta incidencia? Esta acción es irreversible.');">Eliminar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>