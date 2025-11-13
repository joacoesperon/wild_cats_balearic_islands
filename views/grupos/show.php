<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Detalles del Grupo: <?php echo htmlspecialchars($grupo->nombreGrupo); ?></h1>

    <div class="card mb-3">
        <div class="card-header">
            Información General
        </div>
        <div class="card-body">
            <p><strong>ID:</strong> <?php echo htmlspecialchars($grupo->idGrupo); ?></p>
            <p><strong>Nombre del Grupo:</strong> <?php echo htmlspecialchars($grupo->nombreGrupo); ?></p>
            <p><strong>Ayuntamiento:</strong> <?php echo htmlspecialchars($grupo->ayuntamiento_nombre); ?></p>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Miembros del Grupo
        </div>
        <div class="card-body">
            <?php if (empty($miembros)): ?>
                <p>No hay miembros en este grupo.</p>
            <?php else: ?>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Voluntario</th>
                            <th>Usuario</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($miembros as $miembro): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($miembro->nombreCompleto); ?></td>
                                <td><?php echo htmlspecialchars($miembro->usuario); ?></td>
                                <td>
                                    <?php if ($miembro->es_responsable): ?>
                                        <span class="badge bg-primary">Responsable</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Voluntario</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex">
                                        <?php if (!$miembro->es_responsable): ?>
                                            <form action="<?php echo url('grupos/removemiembro'); ?>" method="POST" class="me-2">
                                                <input type="hidden" name="idGrupo" value="<?php echo htmlspecialchars($grupo->idGrupo); ?>">
                                                <input type="hidden" name="idVoluntario" value="<?php echo htmlspecialchars($miembro->idVoluntario); ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar a este voluntario del grupo?');">Quitar</button>
                                            </form>
                                            <form action="<?php echo url('grupos/setresponsable'); ?>" method="POST">
                                                <input type="hidden" name="idGrupo" value="<?php echo htmlspecialchars($grupo->idGrupo); ?>">
                                                <input type="hidden" name="idVoluntario" value="<?php echo htmlspecialchars($miembro->idVoluntario); ?>">
                                                <button type="submit" class="btn btn-sm btn-info">Hacer Responsable</button>
                                            </form>
                                        <?php else: ?>
                                            <small class="text-muted">No se puede quitar al responsable.</small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            Añadir Voluntario al Grupo
        </div>
        <div class="card-body">
            <?php if (!empty($voluntariosDisponibles)): ?>
                <form action="<?php echo url('grupos/addmiembro'); ?>" method="POST" class="row g-3 align-items-end">
                    <input type="hidden" name="idGrupo" value="<?php echo htmlspecialchars($grupo->idGrupo); ?>">
                    <div class="col-md-6">
                        <label for="idVoluntario" class="form-label">Voluntarios Disponibles</label>
                        <select name="idVoluntario" id="idVoluntario" class="form-select" required>
                            <option value="">-- Selecciona un voluntario --</option>
                            <?php foreach ($voluntariosDisponibles as $voluntario): ?>
                                <option value="<?php echo htmlspecialchars($voluntario->idVoluntario); ?>">
                                    <?php echo htmlspecialchars($voluntario->nombreCompleto); ?> (<?php echo htmlspecialchars($voluntario->usuario); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-primary">Añadir Miembro</button>
                    </div>
                </form>
            <?php else: ?>
                <p>No hay más voluntarios disponibles en este ayuntamiento para añadir al grupo.</p>
            <?php endif; ?>
        </div>
    </div>

    <a href="<?php echo url('grupos'); ?>" class="btn btn-secondary">Volver al Listado</a>
    <a href="<?php echo url('grupos/edit?id=' . htmlspecialchars($grupo->idGrupo)); ?>" class="btn btn-warning">Editar Grupo</a>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>