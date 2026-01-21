<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require __DIR__ . '/config/db.php';

$userId = (int) $_SESSION['user_id'];

// Obtener mensajes donde el usuario es emisor o receptor
$stmt = $pdo->prepare('
    SELECT m.id, m.content, m.created_at,
           u.name AS other_name,
           CASE
               WHEN m.to_user_id = :uid THEN "recibido"
               ELSE "enviado"
           END AS direction
    FROM messages m
    JOIN users u
      ON u.id = CASE
                   WHEN m.to_user_id = :uid2 THEN m.from_user_id
                   ELSE m.to_user_id
                END
    WHERE m.to_user_id = :uid3 OR m.from_user_id = :uid4
    ORDER BY m.created_at DESC
');
$stmt->execute([
    ':uid'  => $userId,
    ':uid2' => $userId,
    ':uid3' => $userId,
    ':uid4' => $userId,
]);
$messages = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis mensajes - Abogado en Línea Colombia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles.css?v=3">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<main>
    <section class="hero hero-small">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Mis mensajes</h1>
                <p>Aquí verás los mensajes que has enviado y recibido.</p>
            </div>
        </div>
    </section>

    <section class="results">
        <div class="container">
            <?php if (empty($messages)): ?>
                <p class="card-meta">Aún no tienes mensajes.</p>
            <?php else: ?>
                <div class="messages-list">
                    <?php foreach ($messages as $msg): ?>
                        <article class="card">
                            <p class="card-meta">
                                <?php if ($msg['direction'] === 'recibido'): ?>
                                    De: <?php echo htmlspecialchars($msg['other_name']); ?> ·
                                <?php else: ?>
                                    Para: <?php echo htmlspecialchars($msg['other_name']); ?> ·
                                <?php endif; ?>
                                <?php echo htmlspecialchars($msg['created_at']); ?>
                            </p>
                            <p><?php echo nl2br(htmlspecialchars($msg['content'])); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
