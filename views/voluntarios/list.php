<?php require_once 'views/partials/header.php'; ?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Listado de Voluntarios</h1>
        <a href="/voluntario/create" class="btn btn-primary">Inscribir Nuevo Voluntario</a>
    </div>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th scope="col">Nombre Completo</th>
                <th scope="col">Email</th>
                <th scope="col">Teléfono</th>
                <th scope="col">Ayuntamiento (Bolsa)</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($voluntarios)): ?>
                <tr>
                    <td colspan="5" class="text-center">No hay voluntarios registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($voluntarios as $voluntario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($voluntario['nombreCompleto']); ?></td>
                        <td><?php echo htmlspecialchars($voluntario['email']); ?></td>
                        <td><?php echo htmlspecialchars($voluntario['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($voluntario['ayuntamiento'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="/voluntario/show/<?php echo $voluntario['idVoluntario']; ?>" class="btn btn-sm btn-info">Ver</a>
                            <a href="/voluntario/edit/<?php echo $voluntario['idVoluntario']; ?>" class="btn btn-sm btn-warning">Editar</a>
                            <form action="/voluntario/delete/<?php echo $voluntario['idVoluntario']; ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este voluntario? Se borrarán todos sus datos.');">
                                <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'views/partials/footer.php'; ?>
