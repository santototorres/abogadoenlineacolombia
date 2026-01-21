<?php
session_start();

require __DIR__ . '/config/db.php';

$city      = trim($_GET['city'] ?? '');
$specialty = trim($_GET['specialty'] ?? '');
$mode      = trim($_GET['mode'] ?? '');

// SQL base
$sql = '
    SELECT
        u.id AS user_id,
        u.name,
        lp.city,
        lp.years_experience,
        lp.specialties,
        lp.mode,
        lp.serves_clients,
        lp.headline,
        lp.profile_photo
    FROM users u
    JOIN lawyer_profiles lp ON lp.user_id = u.id
    WHERE u.role = "lawyer"
';
$params = [];

// Filtros
if ($city !== '') {
    $sql .= ' AND lp.city LIKE :city';
    $params[':city'] = '%' . $city . '%';
}
if ($specialty !== '') {
    $sql .= ' AND lp.specialties LIKE :specialty';
    $params[':specialty'] = '%' . $specialty . '%';
}
if ($mode !== '') {
    $sql .= ' AND lp.mode = :mode';
    $params[':mode'] = $mode;
}

$sql .= ' ORDER BY lp.is_verified DESC, lp.years_experience DESC, u.name ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$lawyers = $stmt->fetchAll();

// Ratings promedio por abogado
$ratingsByLawyer = [];
if (!empty($lawyers)) {
    $ids = array_column($lawyers, 'user_id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $sqlRatings = "
        SELECT lawyer_id,
               AVG(rating) AS avg_rating,
               COUNT(*)    AS reviews_count
        FROM reviews
        WHERE lawyer_id IN ($placeholders)
        GROUP BY lawyer_id
    ";
    $stmtR = $pdo->prepare($sqlRatings);
    $stmtR->execute($ids);
    foreach ($stmtR->fetchAll() as $row) {
        $ratingsByLawyer[(int)$row['lawyer_id']] = [
            'avg'   => (float)$row['avg_rating'],
            'count' => (int)$row['reviews_count'],
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar abogado - Abogado en Línea Colombia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles.css?v=8">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<main>
    <section class="hero hero-small">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Resultados de tu búsqueda</h1>
                <p>Ajusta los filtros para encontrar el abogado ideal para tu caso.</p>
            </div>
            <form class="search-box" method="get" action="results.php">
                <div class="search-row">
                    <div class="field">
                        <label for="city">Ciudad</label>
                        <input
                            type="text"
                            id="city"
                            name="city"
                            placeholder="Bogotá, Medellín, Cali..."
                            value="<?php echo htmlspecialchars($city); ?>">
                    </div>
                    <div class="field">
                        <label for="specialty">Especialidad</label>
                        <input
                            type="text"
                            id="specialty"
                            name="specialty"
                            placeholder="Laboral, familia, penal..."
                            value="<?php echo htmlspecialchars($specialty); ?>">
                    </div>
                </div>
                <div class="search-row">
                    <div class="field">
                        <label for="mode">Modalidad</label>
                        <select id="mode" name="mode">
                            <option value="">Virtual o presencial</option>
                            <option value="virtual" <?php echo ($mode === 'virtual') ? 'selected' : ''; ?>>
                                Solo virtual
                            </option>
                            <option value="presencial" <?php echo ($mode === 'presencial') ? 'selected' : ''; ?>>
                                Solo presencial
                            </option>
                            <option value="ambas" <?php echo ($mode === 'ambas') ? 'selected' : ''; ?>>
                                Virtual y presencial
                            </option>
                        </select>
                    </div>
                    <div class="field field-button">
                        <button type="submit" class="btn-primary">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <section class="results">
        <div class="container">
            <div class="results-header">
                <h2>Abogados encontrados</h2>
                <span class="results-count"><?php echo count($lawyers); ?> resultado(s)</span>
            </div>

            <?php if (empty($lawyers)): ?>
                <p class="card-meta">No encontramos abogados con esos filtros. Prueba ajustando la búsqueda.</p>
            <?php else: ?>
                <div class="cards">
                    <?php foreach ($lawyers as $lawyer): ?>
                        <?php
                        $specialtiesText = $lawyer['specialties']
                            ? str_replace(',', ', ', $lawyer['specialties'])
                            : 'Sin especialidades';

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

                        $lawyerId   = (int) $lawyer['user_id'];
                        $ratingInfo = $ratingsByLawyer[$lawyerId] ?? null;
                        $avgRating  = $ratingInfo['avg']   ?? null;
                        $reviewsCnt = $ratingInfo['count'] ?? 0;

                        $stars = '';
                        if ($avgRating !== null) {
                            $fullStars = (int) round($avgRating);
                            for ($i = 1; $i <= 5; $i++) {
                                $stars .= $i <= $fullStars ? '★' : '☆';
                            }
                        }

                        $photoSrc = !empty($lawyer['profile_photo'])
                            ? htmlspecialchars($lawyer['profile_photo'])
                            : 'https://via.placeholder.com/64';
                        ?>
                        <article class="card">
                            <div class="card-header">
                                <img
                                    src="<?php echo $photoSrc; ?>"
                                    alt="Foto de <?php echo htmlspecialchars($lawyer['name']); ?>"
                                    class="card-avatar"
                                >
                                <div>
                                    <h3><?php echo htmlspecialchars($lawyer['name']); ?></h3>
                                    <p class="card-meta">
                                        <?php echo htmlspecialchars($lawyer['city']); ?>
                                        · <?php echo htmlspecialchars($specialtiesText); ?>
                                    </p>
                                </div>
                            </div>
                            <?php if (!empty($lawyer['headline'])): ?>
                                <p class="card-meta"><?php echo htmlspecialchars($lawyer['headline']); ?></p>
                            <?php endif; ?>
                            <p class="card-meta">
                                <?php echo (int) $lawyer['years_experience']; ?> años de experiencia
                                · Atiende: <?php echo htmlspecialchars($clientsText); ?>
                                · Modalidad: <?php echo htmlspecialchars($modeText); ?>
                            </p>
                            <?php if ($avgRating !== null): ?>
                                <p class="card-meta">
                                    <?php echo $stars; ?>
                                    (<?php echo number_format($avgRating, 1); ?> · <?php echo $reviewsCnt; ?> reseña(s))
                                </p>
                            <?php else: ?>
                                <p class="card-meta">Sin reseñas aún</p>
                            <?php endif; ?>
                            <form action="lawyer_profile.php" method="get">
                                <input type="hidden" name="id" value="<?php echo (int) $lawyer['user_id']; ?>">
                                <button class="btn-secondary" type="submit">Ver perfil</button>
                            </form>
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
