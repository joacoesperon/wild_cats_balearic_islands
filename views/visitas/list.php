<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Gestión de Visitas</h1>

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
        <a href="<?php echo url('visitas/create'); ?>" class="btn btn-primary mb-3">Registrar Nueva Visita</a>
    <?php endif; ?>

    <?php if (empty($visitas)): ?>
        <div class="alert alert-info" role="alert">
            No hay visitas registradas.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha Visita</th>
                    <th>Colonia</th>
                    <th>Ayuntamiento</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($visitas as $visita): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($visita->idVisita); ?></td>
                        <td><?php echo htmlspecialchars($visita->fechaVisita); ?></td>
                        <td><?php echo htmlspecialchars($visita->colonia_nombre); ?></td>
                        <td><?php echo htmlspecialchars($visita->ayuntamiento_nombre); ?></td>
                        <td>
                            <a href="<?php echo url('visitas/show?id=' . htmlspecialchars($visita->idVisita)); ?>" class="btn btn-info btn-sm">Ver</a>
                            <?php if ($_SESSION['user_type'] === 'ayuntamiento'): ?>
                                <a href="<?php echo url('visitas/edit?id=' . htmlspecialchars($visita->idVisita)); ?>" class="btn btn-warning btn-sm">Editar</a>
                                <form action="<?php echo url('visitas/delete'); ?>" method="POST" style="display:inline-block;">
                                    <input type="hidden" name="idVisita" value="<?php echo htmlspecialchars($visita->idVisita); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar esta visita? Esta acción es irreversible.');">Eliminar</button>
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