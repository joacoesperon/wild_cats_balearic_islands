<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Gestión de Backups</h1>

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

    <a href="<?php echo url('backups/generate'); ?>" class="btn btn-primary mb-3">Generar Backup Ahora</a>

    <?php if (empty($backups)): ?>
        <div class="alert alert-info" role="alert">
            No hay backups disponibles.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nombre del Archivo</th>
                    <th>Fecha de Creación</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($backups as $backup): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($backup['name']); ?></td>
                        <td><?php echo htmlspecialchars($backup['date']); ?></td>
                                            <td>
                                                <a href="<?php echo url('backups/download?file=' . htmlspecialchars($backup['name'])); ?>" class="btn btn-success btn-sm">Descargar</a>
                                                <form action="<?php echo url('backups/delete'); ?>" method="POST" style="display:inline-block;">
                                                    <input type="hidden" name="fileName" value="<?php echo htmlspecialchars($backup['name']); ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este backup? Esta acción es irreversible.');">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>