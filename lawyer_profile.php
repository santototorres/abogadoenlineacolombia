<?php
session_start();

require __DIR__ . '/config/db.php';

$loggedUserId = $_SESSION['user_id'] ?? null;

$lawyerUserId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($lawyerUserId <= 0) {
    exit('Abogado no encontrado.');
}

$stmt = $pdo->prepare('
    SELECT
        u.id AS user_id,
        u.name,
        u.city AS user_city,
        lp.city AS profile_city,
        lp.headline,
        lp.bio,
        lp.years_experience,
        lp.specialties,
        lp.serves_clients,
        lp.mode,
        lp.profile_photo,
        lp.is_verified
    FROM users u
    JOIN lawyer_profiles lp ON lp.user_id = u.id
    WHERE u.id = ? AND u.role = "lawyer"
    LIMIT 1
');
$stmt->execute([$lawyerUserId]);
$lawyer = $stmt->fetch();

if (!$lawyer) {
    exit('Abogado no encontrado.');
}

// Resumen de rating
$stmt = $pdo->prepare('
    SELECT
        AVG(rating) AS avg_rating,
        COUNT(*)    AS reviews_count
    FROM reviews
    WHERE lawyer_id = ?
');
$stmt->execute([$lawyer['user_id']]);
$ratingSummary = $stmt->fetch();

// Últimas reseñas
$stmt = $pdo->prepare('
    SELECT r.rating, r.comment, r.created_at, u.name AS client_name
    FROM reviews r
    JOIN users u ON u.id = r.client_id
    WHERE r.lawyer_id = ?
    ORDER BY r.created_at DESC
    LIMIT 10
');
$stmt->execute([$lawyer['user_id']]);
$reviews = $stmt->fetchAll();

$errors = [];
$success = false;

// Envío de mensaje
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($loggedUserId)) {
        $errors[] = 'Debes iniciar sesión para enviar un mensaje.';
    } else {
        $content = trim($_POST['content'] ?? '');
        if ($content === '') {
            $errors[] = 'El mensaje no puede estar vacío.';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare('
                INSERT INTO messages (from_user_id, to_user_id, content, created_at)
                VALUES (?, ?, ?, NOW())
            ');
            $stmt->execute([
                (int) $loggedUserId,
                (int) $lawyer['user_id'],
                $content
            ]);

            $success = true;
            $_POST = [];
        }
    }
}

// Textos
$specialtiesText = $lawyer['specialties'] ? str_replace(',', ', ', $lawyer['specialties']) : 'Sin especialidades';
$modeText = match ($lawyer['mode']) {
    'virtual'    => 'Solo virtual',
    'presencial' => 'Solo presencial',
    default      => 'Virtual y presencial',
};
$clientsText = match ($lawyer['serves_clients']) {
    'personas'  => 'Personas',
    'empresas'  => 'Empresas',
    default     => 'Personas y empresas',
};

// Foto
$photoSrc = !empty($lawyer['profile_photo'])
    ? htmlspecialchars($lawyer['profile_photo'])
    : 'https://via.placeholder.com/64';

// Estrellas resumen
$avgRating  = $ratingSummary['avg_rating'] ?? null;
$reviewsCnt = $ratingSummary['reviews_count'] ?? 0;
$starsSummary = '';
if ($avgRating !== null) {
    $fullStars = (int) round($avgRating);
    for ($i = 1; $i <= 5; $i++) {
        $starsSummary .= $i <= $fullStars ? '★' : '☆';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($lawyer['name']); ?> - Abogado en Línea Colombia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles.css?v=8">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<main>
    <section class="hero hero-small">
        <div class="container hero-content">
            <div class="hero-text">
                <h1><?php echo htmlspecialchars($lawyer['name']); ?></h1>
                <p>
                    <?php echo htmlspecialchars($lawyer['headline'] ?: 'Abogado independiente'); ?>
                    · <?php echo htmlspecialchars($lawyer['profile_city'] ?: $lawyer['user_city']); ?>
                </p>
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
                    <img
                        src="<?php echo $photoSrc; ?>"
                        alt="Foto de <?php echo htmlspecialchars($lawyer['name']); ?>"
                        class="card-avatar"
                    >
                    <div>
                        <h3><?php echo htmlspecialchars($lawyer['name']); ?></h3>
                        <p class="card-meta">
                            <?php echo htmlspecialchars($lawyer['profile_city'] ?: $lawyer['user_city']); ?>
                            · <?php echo htmlspecialchars($specialtiesText); ?>
                        </p>
                        <p class="card-meta">
                            <?php echo (int) $lawyer['years_experience']; ?> años de experiencia
                            · Atiende: <?php echo htmlspecialchars($clientsText); ?>
                            · Modalidad: <?php echo htmlspecialchars($modeText); ?>
                        </p>
                        <?php if ($avgRating !== null): ?>
                            <p class="card-meta" style="margin-top:4px;">
                                <?php echo $starsSummary; ?>
                                (<?php echo number_format($avgRating, 1); ?> · <?php echo $reviewsCnt; ?> reseña(s))
                            </p>
                        <?php else: ?>
                            <p class="card-meta" style="margin-top:4px;">Sin reseñas aún</p>
                        <?php endif; ?>

                        <?php if ($loggedUserId): ?>
                            <form action="add_review.php" method="get" style="margin-top:8px; margin-bottom:16px;">
                                <input type="hidden" name="lawyer_id" value="<?php echo (int)$lawyer['user_id']; ?>">
                                <button type="submit" class="btn-secondary">Dejar una reseña</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($lawyer['bio'])): ?>
                    <p class="card-meta" style="margin-top:8px;">
                        <?php echo nl2br(htmlspecialchars($lawyer['bio'])); ?>
                    </p>
                <?php endif; ?>
            </article>

            <?php if (!empty($reviews)): ?>
                <h2 style="margin:16px 0 8px; color: var(--color-primary);">Opiniones de clientes</h2>
                <div class="messages-list">
                    <?php foreach ($reviews as $rev): ?>
                        <?php
                        $revStars = '';
                        for ($i = 1; $i <= 5; $i++) {
                            $revStars .= $i <= (int)$rev['rating'] ? '★' : '☆';
                        }
                        ?>
                        <article class="card">
                            <p class="card-meta">
                                <?php echo $revStars; ?>
                                · <?php echo htmlspecialchars($rev['client_name']); ?>
                                · <?php echo htmlspecialchars($rev['created_at']); ?>
                            </p>
                            <?php if (!empty($rev['comment'])): ?>
                                <p><?php echo nl2br(htmlspecialchars($rev['comment'])); ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" class="form-card" style="margin-top:24px;">

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
