<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Bolsín de Voluntarios</h1>

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

    <a href="<?php echo url('interesados/create'); ?>" class="btn btn-primary mb-3">Registrar Nuevo Interesado</a>

    <?php if (empty($interesados)): ?>
        <div class="alert alert-info" role="alert">
            No hay interesados registrados en el bolsín de este ayuntamiento.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>DNI</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($interesados as $interesado): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($interesado->DNI); ?></td>
                        <td><?php echo htmlspecialchars($interesado->nombreCompleto); ?></td>
                        <td><?php echo htmlspecialchars($interesado->email); ?></td>
                        <td><?php echo htmlspecialchars($interesado->telefono ?? 'N/A'); ?></td>
                        <td>
                            <?php
                                $estado = $interesado->estado;
                                $badge_class = '';
                                switch ($estado) {
                                    case 'Voluntario Activo':
                                        $badge_class = 'bg-success';
                                        break;
                                    case 'Voluntario Inactivo':
                                        $badge_class = 'bg-secondary';
                                        break;
                                    default:
                                        $badge_class = 'bg-info';
                                        break;
                                }
                            ?>
                            <span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($estado); ?></span>
                        </td>
                        <td>
                            <a href="<?php echo url('interesados/edit?id=' . htmlspecialchars($interesado->idInteresado)); ?>" class="btn btn-warning btn-sm">Editar</a>
                            <form action="<?php echo url('interesados/delete'); ?>" method="POST" style="display:inline-block;">
                                <input type="hidden" name="idInteresado" value="<?php echo htmlspecialchars($interesado->idInteresado); ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar a este interesado? Si es voluntario, también se eliminará su registro como voluntario.');">Eliminar</button>
                            </form>
                            <?php if ($interesado->estado === 'Interesado'): ?>
                                <!-- Botón para aceptar como voluntario, que podría abrir un modal para pedir usuario/contraseña -->
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#acceptVolunteerModal" data-id="<?php echo htmlspecialchars($interesado->idInteresado); ?>" data-nombre="<?php echo htmlspecialchars($interesado->nombreCompleto); ?>">
                                    Aceptar como Voluntario
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Modal para aceptar como voluntario -->
<div class="modal fade" id="acceptVolunteerModal" tabindex="-1" aria-labelledby="acceptVolunteerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo url('interesados/accept'); ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="acceptVolunteerModalLabel">Aceptar Interesado como Voluntario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="idInteresado" id="modalIdInteresado">
                    <p>Aceptar a <strong id="modalNombreInteresado"></strong> como voluntario.</p>
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario para el Voluntario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasenya" class="form-label">Contraseña para el Voluntario</label>
                        <input type="password" class="form-control" id="contrasenya" name="contrasenya" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar Aceptación</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var acceptVolunteerModal = document.getElementById('acceptVolunteerModal');
    acceptVolunteerModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget; // Botón que activó el modal
        var idInteresado = button.getAttribute('data-id');
        var nombreInteresado = button.getAttribute('data-nombre');

        var modalIdInteresado = acceptVolunteerModal.querySelector('#modalIdInteresado');
        var modalNombreInteresado = acceptVolunteerModal.querySelector('#modalNombreInteresado');

        modalIdInteresado.value = idInteresado;
        modalNombreInteresado.textContent = nombreInteresado;
    });
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
