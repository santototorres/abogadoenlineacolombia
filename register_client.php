<?php
// Mostrar errores mientras desarrollas (luego puedes quitar estas líneas)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Como config está dentro de public_html:
require __DIR__ . '/config/db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $city     = trim($_POST['city'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    // Validaciones básicas
    if ($name === '') {
        $errors[] = 'El nombre es obligatorio.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El correo electrónico no es válido.';
    }
    if ($password === '' || strlen($password) < 6) {
        $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
    }
    if ($password !== $password_confirm) {
        $errors[] = 'Las contraseñas no coinciden.';
    }

    if (empty($errors)) {
        // Comprobar si ya existe el email
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Ya existe una cuenta con ese correo electrónico.';
        } else {
            // Hash de contraseña
            $password_hash = password_hash($password, PASSWORD_DEFAULT); // [web:102][web:104][web:112]

            $now = date('Y-m-d H:i:s');
            $stmt = $pdo->prepare('
                INSERT INTO users (role, name, email, phone, city, password_hash, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                'client',
                $name,
                $email,
                $phone,
                $city,
                $password_hash,
                $now,
                $now
            ]);

            $success = true;
            // Limpiar campos del formulario después de éxito
            $_POST = [];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro cliente - Abogado en Línea Colombia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles.css?v=3">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>


<main>
    <section class="hero hero-small">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Registro de cliente</h1>
                <p>Crea tu cuenta para contactar abogados verificados en Colombia.</p>
            </div>
        </div>
    </section>

    <section class="results">
        <div class="container">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Tu cuenta ha sido creada. Ya puedes iniciar sesión.
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" class="form-card">
                <div class="form-row">
                    <div class="field">
                        <label for="name">Nombre completo</label>
                        <input type="text" id="name" name="name"
                               value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field">
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field">
                        <label for="phone">Celular</label>
                        <input type="text" id="phone" name="phone"
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                    <div class="field">
                        <label for="city">Ciudad</label>
                        <input type="text" id="city" name="city"
                               value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password">
                    </div>
                    <div class="field">
                        <label for="password_confirm">Confirmar contraseña</label>
                        <input type="password" id="password_confirm" name="password_confirm">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field field-button">
                        <button type="submit" class="btn-primary">Crear cuenta</button>
                    </div>
                </div>
            </form>
        </div>
    </section>
</main>

<footer class="footer">
    <div class="container footer-content">
        <p>© <?php echo date('Y'); ?> Abogado en Línea Colombia</p>
        <div class="footer-links">
            <a href="#">Términos y condiciones</a>
            <a href="#">Política de datos</a>
            <a href="#">Ayuda</a>
        </div>
    </div>
</footer>
</body>
</html>
