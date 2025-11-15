<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenida - Inscripción Voluntarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            text-align: center;
        }
        .welcome-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }
        .welcome-container h1 {
            margin-bottom: 20px;
            color: #28a745; /* Green for welcome */
        }
        .welcome-container p {
            margin-bottom: 30px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h1>¡Bienvenido/a al Programa de Voluntariado!</h1>
        <p>Gracias por tu interés en colaborar con la gestión de colonias felinas. Tu ayuda es muy valiosa para el bienestar de nuestros gatos comunitarios.</p>
        <p>Para empezar, por favor, rellena el siguiente formulario con tus datos personales. Esto nos permitirá añadirte a la bolsa municipal de voluntarios del ayuntamiento que elijas.</p>
        <a href="<?php echo url('interesados/public_create'); ?>" class="btn btn-success btn-lg">Ir al formulario de inscripción</a>
        <a href="<?php echo url('login'); ?>" class="btn btn-secondary btn-lg mt-3">Volver al Login</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>