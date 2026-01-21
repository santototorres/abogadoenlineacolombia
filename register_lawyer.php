<?php
session_start();

require __DIR__ . '/config/db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $city     = trim($_POST['city'] ?? '');
    $headline = trim($_POST['headline'] ?? '');
    $bio      = trim($_POST['bio'] ?? '');
    $years_experience = (int) ($_POST['years_experience'] ?? 0);
    $serves_clients   = $_POST['serves_clients'] ?? 'ambos';
    $mode             = $_POST['mode'] ?? 'ambas';
    $password         = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $specialties      = $_POST['specialties'] ?? [];

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
    if ($city === '') {
        $errors[] = 'La ciudad es obligatoria.';
    }
    if (empty($specialties)) {
        $errors[] = 'Selecciona al menos una especialidad.';
    }

    // Validación de archivo (tarjeta profesional)
    if (
        !isset($_FILES['professional_card']) ||
        $_FILES['professional_card']['error'] !== UPLOAD_ERR_OK
    ) {
        $errors[] = 'Debes subir la tarjeta profesional.';
    }

    // Comprobar email único
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Ya existe una cuenta con ese correo electrónico.';
        }
    }

    // Subida de tarjeta profesional
    if (empty($errors)) {
        $uploadDir = __DIR__ . '/uploads/cards/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $tmpName = $_FILES['professional_card']['tmp_name'];
        $originalName = basename($_FILES['professional_card']['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        $allowed = ['pdf','jpg','jpeg','png'];
        if (!in_array($ext, $allowed, true)) {
            $errors[] = 'Formato de archivo no permitido. Usa PDF o imagen (JPG, PNG).';
        } else {
            $newFileName = 'card_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
            $targetPath  = $uploadDir . $newFileName;
            $relativeCardPath = 'uploads/cards/' . $newFileName;

            if (!move_uploaded_file($tmpName, $targetPath)) {
                $errors[] = 'No se pudo guardar la tarjeta profesional.';
            }
        }
    }

    // Subida de foto de perfil (opcional)
    $profilePhotoRelative = null;
    if (empty($errors) && isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDirPhotos = __DIR__ . '/uploads/avatars/';
        if (!is_dir($uploadDirPhotos)) {
            mkdir($uploadDirPhotos, 0775, true);
        }

        $tmpNamePhoto   = $_FILES['profile_photo']['tmp_name'];
        $originalPhoto  = basename($_FILES['profile_photo']['name']);
        $extPhoto       = strtolower(pathinfo($originalPhoto, PATHINFO_EXTENSION));
        $allowedPhoto   = ['jpg','jpeg','png'];

        if (in_array($extPhoto, $allowedPhoto, true)) {
            $newPhotoName   = 'avatar_' . time() . '_' . mt_rand(1000,9999) . '.' . $extPhoto;
            $targetPhoto    = $uploadDirPhotos . $newPhotoName;
            $profilePhotoRelative = 'uploads/avatars/' . $newPhotoName;

            if (!move_uploaded_file($tmpNamePhoto, $targetPhoto)) {
                $errors[] = 'No se pudo guardar la foto de perfil.';
            }
        } else {
            $errors[] = 'Formato de foto no permitido. Usa JPG o PNG.';
        }
    }

    if (empty($errors)) {
        $pdo->beginTransaction();
        try {
            $now = date('Y-m-d H:i:s');
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Crear usuario
            $stmt = $pdo->prepare('
                INSERT INTO users (role, name, email, phone, city, password_hash, created_at, updated_at)
                VALUES ("lawyer", ?, ?, ?, ?, ?, ?, ?)
            ');
            $stmt->execute([
                $name,
                $email,
                $phone,
                $city,
                $password_hash,
                $now,
                $now
            ]);

            $userId = (int) $pdo->lastInsertId();

            // Especialidades como string
            $specialtiesString = implode(',', array_map('trim', $specialties));

            // Crear perfil de abogado
            $stmt = $pdo->prepare('
                INSERT INTO lawyer_profiles
                    (user_id, headline, bio, city, years_experience, specialties,
                     serves_clients, mode, profile_photo, professional_card_path, is_verified,
                     created_at, updated_at)
                VALUES
                    (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?, ?)
            ');
            $stmt->execute([
                $userId,
                $headline,
                $bio,
                $city,
                $years_experience,
                $specialtiesString,
                $serves_clients,
                $mode,
                $profilePhotoRelative,
                $relativeCardPath,
                $now,
                $now
            ]);

            $pdo->commit();
            $success = true;
            $_POST = [];
        } catch (Throwable $e) {
            $pdo->rollBack();
            $errors[] = 'Ocurrió un error al crear la cuenta de abogado.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro abogado - Abogado en Línea Colombia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles.css?v=8">
</head>
<body>

<?php include __DIR__ . '/header.php'; ?>

<main>
    <section class="hero hero-small">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Registro de abogado</h1>
                <p>Crea tu perfil y empieza a recibir clientes en línea.</p>
            </div>
        </div>
    </section>

    <section class="results">
        <div class="container">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    Tu cuenta de abogado ha sido creada. Un administrador revisará tu tarjeta profesional.
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

            <form method="post" class="form-card" enctype="multipart/form-data">
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
                    <div class="field">
                        <label for="phone">Celular</label>
                        <input type="text" id="phone" name="phone"
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <div class="field">
                        <label for="city">Ciudad</label>
                        <input type="text" id="city" name="city"
                               value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>">
                    </div>
                    <div class="field">
                        <label for="years_experience">Años de experiencia</label>
                        <input type="number" id="years_experience" name="years_experience" min="0" max="60"
                               value="<?php echo htmlspecialchars($_POST['years_experience'] ?? '0'); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label>Especialidades</label>
                        <div class="checkbox-group">
                            <?php
                            $allSpecs = ['laboral','familia','penal','empresarial','inmobiliario','civil','administrativo','contractual'];
                            $labels = [
                                'laboral'        => 'Derecho laboral',
                                'familia'        => 'Derecho de familia',
                                'penal'          => 'Derecho penal',
                                'empresarial'    => 'Derecho empresarial',
                                'inmobiliario'   => 'Derecho inmobiliario',
                                'civil'          => 'Derecho civil',
                                'administrativo' => 'Derecho administrativo',
                                'contractual'    => 'Derecho contractual',
                            ];
                            $selectedSpecs = $_POST['specialties'] ?? [];
                            foreach ($allSpecs as $spec): ?>
                                <label class="checkbox-item">
                                    <input type="checkbox" name="specialties[]"
                                           value="<?php echo $spec; ?>"
                                        <?php echo in_array($spec, $selectedSpecs ?? [], true) ? 'checked' : ''; ?>>
                                    <?php echo $labels[$spec] ?? ucfirst($spec); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="headline">Titular del perfil</label>
                        <input type="text" id="headline" name="headline"
                               placeholder="Ej: Abogado laboral para personas y pymes"
                               value="<?php echo htmlspecialchars($_POST['headline'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="bio">Descripción / Bio</label>
                        <textarea id="bio" name="bio" rows="4"
                                  style="border-radius: 12px; padding: 10px 12px; border: 1px solid var(--color-border);"><?php
                            echo htmlspecialchars($_POST['bio'] ?? '');
                        ?></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="serves_clients">Tipo de clientes</label>
                        <select id="serves_clients" name="serves_clients">
                            <option value="ambos" <?php echo (($_POST['serves_clients'] ?? '') === 'ambos') ? 'selected' : ''; ?>>
                                Personas y empresas
                            </option>
                            <option value="personas" <?php echo (($_POST['serves_clients'] ?? '') === 'personas') ? 'selected' : ''; ?>>
                                Solo personas
                            </option>
                            <option value="empresas" <?php echo (($_POST['serves_clients'] ?? '') === 'empresas') ? 'selected' : ''; ?>>
                                Solo empresas
                            </option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="mode">Modalidad de atención</label>
                        <select id="mode" name="mode">
                            <option value="ambas" <?php echo (($_POST['mode'] ?? '') === 'ambas') ? 'selected' : ''; ?>>
                                Virtual y presencial
                            </option>
                            <option value="virtual" <?php echo (($_POST['mode'] ?? '') === 'virtual') ? 'selected' : ''; ?>>
                                Solo virtual
                            </option>
                            <option value="presencial" <?php echo (($_POST['mode'] ?? '') === 'presencial') ? 'selected' : ''; ?>>
                                Solo presencial
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="profile_photo">Foto de perfil (opcional)</label>
                        <input type="file" id="profile_photo" name="profile_photo" accept=".jpg,.jpeg,.png">
                    </div>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="professional_card">Tarjeta profesional (PDF / imagen)</label>
                        <input type="file" id="professional_card" name="professional_card" accept=".pdf,.jpg,.jpeg,.png">
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
                        <button type="submit" class="btn-primary">Crear perfil de abogado</button>
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
