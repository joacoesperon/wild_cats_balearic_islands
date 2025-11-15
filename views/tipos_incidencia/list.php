<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Gestión de Tipos de Incidencia</h1>
    <p>Aquí puedes definir los tipos de incidencia que pueden ocurrir en una visita (ej: "Gato enfermo", "Falta de comida").</p>

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

    <a href="<?php echo url('tipos_incidencia/create'); ?>" class="btn btn-primary mb-3">Crear Nuevo Tipo</a>

    <?php if (empty($tipos)): ?>
        <div class="alert alert-info" role="alert">
            No hay tipos de incidencia definidos.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tipos as $tipo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tipo->idIncidencia); ?></td>
                        <td><?php echo htmlspecialchars($tipo->textoDescriptivo); ?></td>
                        <td>
                            <a href="<?php echo url('tipos_incidencia/edit?id=' . htmlspecialchars($tipo->idIncidencia)); ?>" class="btn btn-warning btn-sm">Editar</a>
                            <form action="<?php echo url('tipos_incidencia/delete'); ?>" method="POST" style="display:inline-block;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($tipo->idIncidencia); ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro? Si este tipo de incidencia ya está en uso, no se podrá eliminar.');">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
