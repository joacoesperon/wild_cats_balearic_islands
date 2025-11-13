<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <h1>Bienvenido, <?php echo htmlspecialchars($ayuntamiento->nombreLocalidad); ?>!</h1>
    <p>Este es el panel de control de tu ayuntamiento. Desde aquí puedes gestionar las colonias, gatos, grupos, voluntarios, visitas e incidencias.</p>

    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Colonias</h5>
                    <p class="card-text">Gestiona las colonias felinas de tu municipio.</p>
                    <a href="<?php echo url('colonias'); ?>" class="btn btn-primary">Ir a Colonias</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Gatos</h5>
                    <p class="card-text">Administra la información de los gatos.</p>
                    <a href="<?php echo url('gatos'); ?>" class="btn btn-primary">Ir a Gatos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Grupos</h5>
                    <p class="card-text">Organiza tus grupos de voluntarios.</p>
                    <a href="<?php echo url('grupos'); ?>" class="btn btn-primary">Ir a Grupos</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Tareas</h5>
                    <p class="card-text">Gestiona las tareas asignadas a los grupos.</p>
                    <a href="<?php echo url('trabajos'); ?>" class="btn btn-primary">Ir a Tareas</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Bolsín de Voluntarios</h5>
                    <p class="card-text">Gestiona los interesados y voluntarios.</p>
                    <a href="<?php echo url('interesados'); ?>" class="btn btn-primary">Ir a Voluntarios</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Visitas</h5>
                    <p class="card-text">Registra y consulta las visitas a colonias.</p>
                    <a href="<?php echo url('visitas'); ?>" class="btn btn-primary">Ir a Visitas</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Incidencias</h5>
                    <p class="card-text">Reporta y gestiona las incidencias.</p>
                    <a href="<?php echo url('incidencias'); ?>" class="btn btn-primary">Ir a Incidencias</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Backups</h5>
                    <p class="card-text">Gestiona las copias de seguridad de la base de datos.</p>
                    <a href="<?php echo url('backups'); ?>" class="btn btn-primary">Ir a Backups</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>
