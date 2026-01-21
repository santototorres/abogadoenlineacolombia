<?php
session_start();

require __DIR__ . '/config/db.php';

$loggedUserId = $_SESSION['user_id'] ?? null;
$loggedRole   = $_SESSION['user_role'] ?? null;

if (empty($loggedUserId)) {
    header('Location: login.php');
    exit;
}

// Tomar el abogado desde la URL
$lawyerId = isset($_GET['lawyer_id']) ? (int) $_GET['lawyer_id'] : 0;
if ($lawyerId <= 0) {
    exit('Abogado no válido.');
}

// Comprobar que el abogado existe
$stmt = $pdo->prepare('SELECT id, name FROM users WHERE id = ? AND role = "lawyer" LIMIT 1');
$stmt->execute([$lawyerId]);
$lawyer = $stmt->fetch();

if (!$lawyer) {
    exit('Abogado no encontrado.');
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating  = (int) ($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($rating < 1 || $rating > 5) {
        $errors[] = 'Selecciona una calificación entre 1 y 5 estrellas.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('
            INSERT INTO reviews (client_id, lawyer_id, rating, comment, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ');
        $stmt->execute([
            (int) $loggedUserId,
            (int) $lawyer['id'],
            $rating,
            $comment
        ]); // [web:214][web:241]

        $success = true;
        $_POST = [];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dejar reseña - Abogado en Línea Colombia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles.css?v=7">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<main>
    <section class="hero hero-small">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Dejar reseña</h1>
                <p>Califica tu experiencia con <?php echo htmlspecialchars($lawyer['name']); ?>.</p>
            </div>
        </div>
    </section>

    <section class="results">
        <div class="container">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Tu reseña ha sido registrada. Gracias por tu opinión.
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
                        <label for="rating">Calificación (1 a 5 estrellas)</label>
                        <select id="rating" name="rating">
                            <option value="">Selecciona...</option>
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?php echo $i; ?>" <?php echo (($_POST['rating'] ?? '') == $i) ? 'selected' : ''; ?>>
                                    <?php echo $i; ?> estrella(s)
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="field">
                        <label for="comment">Comentario (opcional)</label>
                        <textarea id="comment" name="comment" rows="4"
                                  style="border-radius: 12px; padding: 10px 12px; border: 1px solid var(--color-border);"><?php
                            echo htmlspecialchars($_POST['comment'] ?? '');
                        ?></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <div class="field field-button">
                        <button type="submit" class="btn-primary">Enviar reseña</button>
                    </div>
                </div>
            </form>

            <p class="card-meta" style="margin-top:12px;">
                Nota: Más adelante solo permitiremos reseñas de clientes con citas completadas.
            </p>
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
