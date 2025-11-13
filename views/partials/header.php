<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Colonias Felinas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo rtrim(dirname($_SERVER['SCRIPT_NAME']), '/'); ?>/public/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?php
                if (isset($_SESSION['user_type'])) {
                    if ($_SESSION['user_type'] === 'ayuntamiento') {
                        echo url('ayuntamientos');
                    } elseif ($_SESSION['user_type'] === 'voluntario') {
                        echo url('voluntarios/show?id=' . $_SESSION['user_id']);
                    } else {
                        echo url('login');
                    }
                } else {
                    echo url('login');
                }
            ?>">Gestión Felina</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <?php if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'ayuntamiento'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('colonias'); ?>">Colonias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('gatos'); ?>">Gatos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('grupos'); ?>">Grupos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('trabajos'); ?>">Tareas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('interesados'); ?>">Bolsín Voluntarios</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('visitas'); ?>">Visitas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('incidencias'); ?>">Incidencias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('backups'); ?>">Backups</a>
                        </li>
                    <?php elseif (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'voluntario'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('voluntarios/show?id=' . $_SESSION['user_id']); ?>">Mi Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('visitas'); ?>">Mis Visitas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('incidencias'); ?>">Mis Incidencias</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['username'])): ?>
                        <li class="nav-item">
                            <span class="nav-link text-light">Bienvenido, <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo ucfirst($_SESSION['user_type']); ?>)</span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light ms-2" href="<?php echo url('logout'); ?>">Cerrar Sesión</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-outline-light" href="<?php echo url('login'); ?>">Iniciar Sesión</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">