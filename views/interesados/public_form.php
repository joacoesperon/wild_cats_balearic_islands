<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción de Voluntario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .form-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }
        .form-container h1 {
            margin-bottom: 20px;
            color: #343a40;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1 class="text-center">Formulario de Inscripción de Voluntario</h1>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo url('interesados/public_store'); ?>" method="POST">
            <div class="mb-3">
                <label for="DNI" class="form-label">DNI</label>
                <input type="text" class="form-control" id="DNI" name="DNI" value="<?php echo htmlspecialchars($interesado->DNI ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="nombreCompleto" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="nombreCompleto" name="nombreCompleto" value="<?php echo htmlspecialchars($interesado->nombreCompleto ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($interesado->email ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($interesado->telefono ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="idAyuntamiento" class="form-label">Ayuntamiento al que deseas unirte</label>
                <select class="form-select" id="idAyuntamiento" name="idAyuntamiento" required>
                    <option value="">-- Seleccionar Ayuntamiento --</option>
                    <?php foreach ($ayuntamientos as $ayuntamiento): ?>
                        <option value="<?php echo htmlspecialchars($ayuntamiento->idAyuntamiento); ?>"
                            <?php echo (isset($interesado) && $interesado->idAyuntamiento == $ayuntamiento->idAyuntamiento) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($ayuntamiento->nombreLocalidad); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Enviar Solicitud</button>
            <a href="<?php echo url('interesados/public_welcome'); ?>" class="btn btn-secondary w-100 mt-2">Cancelar</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>