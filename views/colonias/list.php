<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Gestión de Colonias</h1>

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

    <a href="<?php echo url('colonias/create'); ?>" class="btn btn-primary mb-3">Añadir Nueva Colonia</a>

    <?php if (empty($colonias)): ?>
        <div class="alert alert-info" role="alert">
            No hay colonias registradas para este ayuntamiento.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Ubicación</th>
                    <th>Latitud</th>
                    <th>Longitud</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($colonias as $colonia): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($colonia->idColonia); ?></td>
                        <td><?php echo htmlspecialchars($colonia->descripcion); ?></td>
                        <td><?php echo htmlspecialchars($colonia->ubicacion_descripcion); ?></td>
                        <td><?php echo htmlspecialchars($colonia->latitud); ?></td>
                        <td><?php echo htmlspecialchars($colonia->longitud); ?></td>
                        <td>
                            <a href="<?php echo url('colonias/show?id=' . htmlspecialchars($colonia->idColonia)); ?>" class="btn btn-info btn-sm">Ver</a>
                            <a href="<?php echo url('colonias/edit?id=' . htmlspecialchars($colonia->idColonia)); ?>" class="btn btn-warning btn-sm">Editar</a>
                            <form action="<?php echo url('colonias/delete'); ?>" method="POST" style="display:inline-block;">
                                <input type="hidden" name="idColonia" value="<?php echo htmlspecialchars($colonia->idColonia); ?>">
                                <input type="hidden" name="idUbicacion" value="<?php echo htmlspecialchars($colonia->idUbicacion); ?>">
                                <input type="hidden" name="idCoordenada" value="<?php echo htmlspecialchars($colonia->idCoordenada); ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar esta colonia? Esta acción es irreversible y eliminará todos los gatos asociados.');">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>