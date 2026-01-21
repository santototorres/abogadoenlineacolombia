<?php
session_start();

require __DIR__ . '/config/db.php';

$errors = [];
$success = false;

// ID del abogado de prueba (ajusta al id real que viste en la tabla users)
$lawyerIdDummy = 2;

// Datos del abogado de prueba desde la BD
$stmt = $pdo->prepare('SELECT id, name, city FROM users WHERE id = ? AND role = "lawyer" LIMIT 1');
$stmt->execute([$lawyerIdDummy]);
$lawyer = $stmt->fetch();

if (!$lawyer) {
    exit('Abogado de prueba no encontrado.');
}

// Enviar mensaje
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['user_id'])) {
        $errors[] = 'Debes iniciar sesión para enviar un mensaje.';
    } else {
        $fromUserId = (int) $_SESSION['user_id'];
        $toUserId   = (int) $lawyer['id'];
        $content    = trim($_POST['content'] ?? '');

        if ($content === '') {
            $errors[] = 'El mensaje no puede estar vacío.';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare('
                INSERT INTO messages (from_user_id, to_user_id, content, created_at)
                VALUES (?, ?, ?, NOW())
            ');
            $stmt->execute([$fromUserId, $toUserId, $content]); // [web:160][web:170][web:169]

            $success = true;
            $_POST = [];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Perfil de abogado - Abogado en Línea Colombia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles.css?v=3">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<main>
    <section class="hero hero-small">
        <div class="container hero-content">
            <div class="hero-text">
                <h1><?php echo htmlspecialchars($lawyer['name']); ?></h1>
                <p>Abogado independiente en <?php echo htmlspecialchars($lawyer['city']); ?> (perfil de prueba).</p>
            </div>
        </div>
    </section>

    <section class="results">
        <div class="container">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Tu mensaje ha sido enviado al abogado.
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

            <article class="card" style="margin-bottom:16px;">
                <div class="card-header">
                    <img src="https://via.placeholder.com/64"
                         alt="Foto de <?php echo htmlspecialchars($lawyer['name']); ?>"
                         class="card-avatar">
                    <div>
                        <h3><?php echo htmlspecialchars($lawyer['name']); ?></h3>
                        <p class="card-meta"><?php echo htmlspecialchars($lawyer['city']); ?> · Especialidad (dummy)</p>
                        <p class="card-meta">Años de experiencia, modalidad, etc. (dummy)</p>
                    </div>
                </div>
                <p class="card-meta">
                    Aquí luego mostraremos la bio completa, especialidades, tarifas y calificaciones del abogado.
                </p>
            </article>

            <form method="post" class="form-card">
                <div class="form-row">
                    <div class="field">
                        <label for="content">Enviar mensaje al abogado</label>
                        <textarea id="content" name="content" rows="4"
                                  style="border-radius: 12px; padding: 10px 12px; border: 1px solid var(--color-border);"><?php
                            echo htmlspecialchars($_POST['content'] ?? '');
                        ?></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="field field-button">
                        <button type="submit" class="btn-primary">Enviar mensaje</button>
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
