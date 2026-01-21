<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userName = $_SESSION['user_name'] ?? null;
?>
<header class="header">
    <div class="container header-content">
        
        
        <div class="logo">
    <a href="index.php" class="logo-link">
        <img src="images/abogado_en_linea_logo.png"
             alt="Abogado en Línea Colombia"
             class="logo-img">
    </a>
</div>

        
        
        <nav class="nav">
            <a href="index.php" class="nav-link">Encontrar abogado</a>
            <a href="register_lawyer.php" class="nav-link">Soy abogado</a>

            <?php if ($userName): ?>
                <a href="messages.php" class="nav-link">Mensajes</a>
                <span class="nav-link">Hola, <?php echo htmlspecialchars($userName); ?></span>
                <a href="logout.php" class="nav-link">Cerrar sesión</a>
            <?php else: ?>
                <a href="login.php" class="nav-link">Ingresar</a>
                <a href="register_client.php" class="nav-link nav-primary">Registrarme</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
