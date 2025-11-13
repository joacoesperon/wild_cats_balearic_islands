<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Gestión de Gatos</h1>

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

    <a href="<?php echo url('gatos/create'); ?>" class="btn btn-primary mb-3">Añadir Nuevo Gato</a>

    <?php if (empty($gatos)): ?>
        <div class="alert alert-info" role="alert">
            No hay gatos registrados para las colonias de este ayuntamiento.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nombre</th>
                    <th>Chip</th>
                    <th>Sexo</th>
                    <th>Colonia Actual</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($gatos as $gato): ?>
                    <tr>
                        <td>
                            <?php if ($gato->foto): ?>
                                <img src="<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/public/uploads/gatos/<?php echo htmlspecialchars($gato->foto); ?>" alt="Foto de <?php echo htmlspecialchars($gato->nombre); ?>" class="gato-img-thumbnail">
                            <?php else: ?>
                                <img src="<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/public/img/default_cat.png" alt="Sin foto" class="gato-img-thumbnail">
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($gato->nombre); ?></td>
                        <td><?php echo htmlspecialchars($gato->numeroChip ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($gato->sexo); ?></td>
                        <td><?php echo htmlspecialchars($gato->colonia_actual ?? 'Sin colonia'); ?></td>
                        <td>
                            <a href="<?php echo url('gatos/show?id=' . htmlspecialchars($gato->idGato)); ?>" class="btn btn-info btn-sm">Ver</a>
                            <a href="<?php echo url('gatos/edit?id=' . htmlspecialchars($gato->idGato)); ?>" class="btn btn-warning btn-sm">Editar</a>
                            <form action="<?php echo url('gatos/delete'); ?>" method="POST" style="display:inline-block;">
                                <input type="hidden" name="idGato" value="<?php echo htmlspecialchars($gato->idGato); ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este gato? Esta acción es irreversible.');">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>