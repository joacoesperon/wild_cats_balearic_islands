<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Detalles del Gato: <?php echo htmlspecialchars($gato->nombre); ?></h1>

    <div class="card mb-3">
        <div class="card-header">
            Información General
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center">
                    <?php if ($gato->foto): ?>
                        <img src="<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/public/uploads/gatos/<?php echo htmlspecialchars($gato->foto); ?>" alt="Foto de <?php echo htmlspecialchars($gato->nombre); ?>" class="img-fluid rounded gato-img-thumbnail-lg">
                    <?php else: ?>
                        <img src="<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/public/img/default_cat.png" alt="Sin foto" class="img-fluid rounded gato-img-thumbnail-lg">
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <p><strong>ID:</strong> <?php echo htmlspecialchars($gato->idGato); ?></p>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($gato->nombre); ?></p>
                    <p><strong>Descripción de Aspecto:</strong> <?php echo htmlspecialchars($gato->descripcionAspecto); ?></p>
                    <p><strong>Número de Chip:</strong> <?php echo htmlspecialchars($gato->numeroChip ?? 'N/A'); ?></p>
                    <p><strong>Sexo:</strong> <?php echo htmlspecialchars($gato->sexo); ?></p>
                    <p><strong>Colonia Actual:</strong>
                        <?php if ($gato->colonia_actual): ?>
                            <a href="<?php echo url('colonias/show?id=' . htmlspecialchars($gato->idColoniaActual)); ?>">
                                <?php echo htmlspecialchars($gato->colonia_actual); ?>
                            </a>
                        <?php else: ?>
                            Sin colonia asignada
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Historial de Estancias
        </div>
        <div class="card-body">
            <?php if (empty($estancias)): ?>
                <p>No hay historial de estancias para este gato.</p>
            <?php else: ?>
                <table class="table table-sm table-striped">
                    <thead>
                        <tr>
                            <th>Colonia</th>
                            <th>Ayuntamiento</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estancias as $estancia): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($estancia->colonia_nombre); ?></td>
                                <td><?php echo htmlspecialchars($estancia->ayuntamiento_nombre); ?></td>
                                <td><?php echo htmlspecialchars($estancia->fechaInicio); ?></td>
                                <td><?php echo htmlspecialchars($estancia->fechaFin ?? 'Actual'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <a href="<?php echo url('gatos'); ?>" class="btn btn-secondary">Volver al Listado</a>
    <a href="<?php echo url('gatos/edit?id=' . htmlspecialchars($gato->idGato)); ?>" class="btn btn-warning">Editar Gato</a>
</div>

<style>
    .gato-img-thumbnail-lg {
        width: 200px;
        height: 200px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #ddd;
        padding: 5px;
    }
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>