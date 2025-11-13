<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Detalles de la Colonia: <?php echo htmlspecialchars($colonia->descripcion); ?></h1>

    <div class="card mb-3">
        <div class="card-header">
            Información General
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($colonia->idColonia); ?></p>
            <p><strong>Descripción:</strong> <?php echo htmlspecialchars($colonia->descripcion); ?></p>
            <p><strong>Comentarios:</strong> <?php echo htmlspecialchars($colonia->comentarios); ?></p>
            <p><strong>Ayuntamiento:</strong> <?php echo htmlspecialchars($colonia->ayuntamiento_nombre); ?></p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Ubicación
        </div>
        <div class="card-body">
            <p><strong>Descripción de Ubicación:</strong> <?php echo htmlspecialchars($colonia->ubicacion_descripcion); ?></p>
            <p><strong>Latitud:</strong> <?php echo htmlspecialchars($colonia->latitud); ?></p>
            <p><strong>Longitud:</strong> <?php echo htmlspecialchars($colonia->longitud); ?></p>
            <!-- Aquí se podría integrar un mapa con las coordenadas -->
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Gatos en esta Colonia
        </div>
        <div class="card-body">
            <?php if (!empty($gatos)): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nombre</th>
                            <th>Sexo</th>
                            <th>Nº Chip</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gatos as $gato): ?>
                            <tr>
                                <td>
                                    <img src="<?php echo url('public/uploads/gatos/' . (!empty($gato->foto) ? htmlspecialchars($gato->foto) : 'default_cat.png')); ?>" alt="Foto de <?php echo htmlspecialchars($gato->nombre); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                </td>
                                <td><?php echo htmlspecialchars($gato->nombre); ?></td>
                                <td><?php echo htmlspecialchars($gato->sexo); ?></td>
                                <td><?php echo htmlspecialchars($gato->numeroChip ?: 'N/A'); ?></td>
                                <td>
                                    <a href="<?php echo url('gatos/show?id=' . htmlspecialchars($gato->idGato)); ?>" class="btn btn-info btn-sm">Ver</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay gatos registrados en esta colonia actualmente.</p>
            <?php endif; ?>
        </div>
    </div>

    <a href="<?php echo url('colonias'); ?>" class="btn btn-secondary">Volver al Listado</a>
    <a href="<?php echo url('colonias/edit?id=' . htmlspecialchars($colonia->idColonia)); ?>" class="btn btn-warning">Editar Colonia</a>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>