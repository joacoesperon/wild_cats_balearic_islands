<?php require_once 'views/partials/header.php'; ?>

<?php
$is_edit = isset($voluntario);
$action_url = $is_edit ? '/voluntario/update/' . $voluntario['idVoluntario'] : '/voluntario/store';
$page_title = $is_edit ? 'Editar Voluntario' : 'Inscribir Nuevo Voluntario';
?>

<div class="container">
    <h1><?php echo $page_title; ?></h1>

    <form action="<?php echo $action_url; ?>" method="POST">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nombreCompleto" class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control" id="nombreCompleto" name="nombreCompleto" value="<?php echo htmlspecialchars($voluntario['nombreCompleto'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="DNI" class="form-label">DNI</label>
                    <input type="text" class="form-control" id="DNI" name="DNI" value="<?php echo htmlspecialchars($voluntario['DNI'] ?? ''); ?>" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($voluntario['email'] ?? ''); ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($voluntario['telefono'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <hr>
        <h5>Datos de Acceso y Asignación</h5>

        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="contrasenya" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="contrasenya" name="contrasenya" <?php echo $is_edit ? '' : 'required'; ?>>
                    <?php if ($is_edit): ?>
                        <div class="form-text">Dejar en blanco para no cambiar la contraseña actual.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="idBolsaMunicipal" class="form-label">Bolsa Municipal de Voluntarios</label>
                    <select class="form-select" id="idBolsaMunicipal" name="idBolsaMunicipal" <?php echo $is_edit ? 'disabled' : 'required'; ?>>
                        <option value="">Seleccione una bolsa</option>
                        <?php foreach ($bolsas as $bolsa): ?>
                            <option value="<?php echo $bolsa['idBolsaMunicipal']; ?>">
                                <?php echo htmlspecialchars($bolsa['nombre'] . ' (' . $bolsa['nombreLocalidad'] . ')'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                     <?php if ($is_edit): ?>
                        <div class="form-text">La bolsa de un voluntario no se puede cambiar.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="/voluntario/list" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary"><?php echo $is_edit ? 'Actualizar' : 'Guardar'; ?></button>
        </div>
    </form>
</div>

<?php require_once 'views/partials/footer.php'; ?>
