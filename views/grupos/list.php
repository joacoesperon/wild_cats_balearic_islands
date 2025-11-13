<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Gestión de Grupos</h1>

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

    <a href="<?php echo url('grupos/create'); ?>" class="btn btn-primary mb-3">Añadir Nuevo Grupo</a>

    <?php if (empty($grupos)): ?>
        <div class="alert alert-info" role="alert">
            No hay grupos registrados para este ayuntamiento.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre del Grupo</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grupos as $grupo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($grupo->idGrupo); ?></td>
                        <td><?php echo htmlspecialchars($grupo->nombreGrupo); ?></td>
                        <td>
                            <a href="<?php echo url('grupos/show?id=' . htmlspecialchars($grupo->idGrupo)); ?>" class="btn btn-info btn-sm">Ver</a>
                            <a href="<?php echo url('grupos/edit?id=' . htmlspecialchars($grupo->idGrupo)); ?>" class="btn btn-warning btn-sm">Editar</a>
                            <form action="<?php echo url('grupos/delete'); ?>" method="POST" style="display:inline-block;">
                                <input type="hidden" name="idGrupo" value="<?php echo htmlspecialchars($grupo->idGrupo); ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este grupo? Esta acción eliminará también sus pertenencias.');">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>