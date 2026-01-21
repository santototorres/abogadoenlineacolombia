<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Abogado en Línea Colombia</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>


<main>
    <!-- Hero / Buscador -->
    <section class="hero">
        <div class="container hero-content">
            <div class="hero-text">
                <h1>Encuentra tu abogado en Colombia</h1>
                <p>Conecta con abogados verificados para personas y empresas, en modalidad virtual o presencial.</p>
            </div>
            <form class="search-box">
                <div class="search-row">
                    <div class="field">
                        <label for="city">Ciudad</label>
                        <input type="text" id="city" name="city" placeholder="Bogotá, Medellín, Cali...">
                    </div>
                    <div class="field">
                        <label for="specialty">Especialidad</label>
                        <input type="text" id="specialty" name="specialty" placeholder="Laboral, familia, penal...">
                    </div>
                </div>
                <div class="search-row">
                    <div class="field">
                        <label for="mode">Modalidad</label>
                        <select id="mode" name="mode">
                            <option value="">Virtual o presencial</option>
                            <option value="virtual">Solo virtual</option>
                            <option value="presencial">Solo presencial</option>
                        </select>
                    </div>
                    <div class="field field-button">
                        <button type="submit" class="btn-primary">Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Cómo funciona -->
    <section class="how-it-works">
        <div class="container">
            <h2>¿Cómo funciona?</h2>
            <div class="steps">
                <div class="step">
                    <h3>1. Busca</h3>
                    <p>Filtra por ciudad, especialidad y modalidad para encontrar abogados que se ajusten a tu caso.</p>
                </div>
                <div class="step">
                    <h3>2. Elige</h3>
                    <p>Revisa perfiles, experiencia y calificaciones de otros usuarios antes de tomar una decisión.</p>
                </div>
                <div class="step">
                    <h3>3. Agenda</h3>
                    <p>Agenda una cita virtual o presencial y coordina los detalles desde la plataforma.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Abogados destacados -->
    <section class="featured">
        <div class="container">
            <h2>Abogados destacados</h2>
            <div class="cards">
                <article class="card">
                    <span class="badge">Premium</span>
                    <h3>Laura Pérez</h3>
                    <p class="card-meta">Bogotá · Derecho laboral</p>
                    <p class="card-meta">8 años de experiencia · ★ 4.9</p>
                </article>
                <article class="card">
                    <span class="badge">Premium</span>
                    <h3>Carlos Gómez</h3>
                    <p class="card-meta">Medellín · Derecho de familia</p>
                    <p class="card-meta">10 años de experiencia · ★ 4.8</p>
                </article>
                <article class="card">
                    <h3>Ana Rodríguez</h3>
                    <p class="card-meta">Cali · Derecho empresarial</p>
                    <p class="card-meta">6 años de experiencia · ★ 4.7</p>
                </article>
            </div>
        </div>
    </section>

    <!-- Confianza -->
    <section class="trust">
        <div class="container trust-content">
            <h2>Confianza y verificación</h2>
            <p>
                Verificamos manualmente la tarjeta profesional de cada abogado y recogemos calificaciones reales de usuarios,
                para que puedas tomar decisiones informadas.
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
