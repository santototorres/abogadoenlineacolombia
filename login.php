<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__ . '/config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'El correo electrónico no es válido.';
    }
    if ($password === '') {
        $errors[] = 'La contraseña es obligatoria.';
    }

    if (empty($errors)) {
        // Buscar usuario por email
        $stmt = $pdo->prepare('SELECT id, role, name, email, password_hash FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) { // [web:131][web:132][web:140]
            // Login correcto: guardar en sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Por ahora, redirigimos al inicio
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Credenciales incorrectas.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingresar - Abogado en Línea Colombia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles.css?v=3">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>


<main>
    <section class="hero hero-small">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Ingresar</h1>
                <p>Accede a tu cuenta para gestionar tus consultas y citas.</p>
            </div>
        </div>
    </section>

    <section class="results">
        <div class="container">
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
                        <label for="email">Correo electrónico</label>
                        <input type="email" id="email" name="email"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field">
                        <label for="password">Contraseña</label>
                        <input type="password" id="password" name="password">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field field-button">
                        <button type="submit" class="btn-primary">Ingresar</button>
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
