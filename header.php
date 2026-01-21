/* ============================================
   ABOGADO EN LÍNEA COLOMBIA - RESPONSIVE
   ============================================ */

:root {
  /* Colores */
  --primary: #1e3a5f;
  --primary-light: #2d5a8f;
  --accent: #d4af37;
  --accent-hover: #b8941f;
  --secondary: #e74c3c;
  --secondary-hover: #c0392b;
  
  --bg-main: #f8f9fa;
  --bg-white: #ffffff;
  --bg-light: #f1f3f5;
  
  --text-dark: #2c3e50;
  --text-medium: #5a6c7d;
  --text-light: #95a5a6;
  
  --border: #dee2e6;
  --border-light: #e9ecef;
  
  --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
  --shadow-md: 0 4px 16px rgba(0,0,0,0.12);
  --shadow-lg: 0 8px 24px rgba(0,0,0,0.15);
  --shadow-xl: 0 16px 48px rgba(0,0,0,0.2);
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

html {
  scroll-behavior: smooth;
  font-size: 16px;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
  line-height: 1.6;
  color: var(--text-dark);
  background: var(--bg-main);
  overflow-x: hidden;
}

/* TIPOGRAFÍA */
h1, h2, h3, h4, h5, h6 {
  font-weight: 700;
  line-height: 1.3;
  color: var(--primary);
  margin-bottom: 1rem;
}

h1 { font-size: clamp(1.75rem, 4vw, 3rem); }
h2 { font-size: clamp(1.5rem, 3.5vw, 2.25rem); }
h3 { font-size: clamp(1.25rem, 3vw, 1.75rem); }

p {
  margin-bottom: 1rem;
}

/* CONTAINER CON MÁRGENES */
.container {
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1rem;
}

@media (min-width: 640px) {
  .container { padding: 0 1.5rem; }
}

@media (min-width: 1024px) {
  .container { padding: 0 2rem; }
}

/* ============================================
   HEADER CON MENÚ HAMBURGUESA
   ============================================ */
.header {
  background: var(--bg-white);
  box-shadow: var(--shadow-sm);
  position: sticky;
  top: 0;
  z-index: 1000;
  transition: box-shadow 0.3s ease;
}

.header.scrolled {
  box-shadow: var(--shadow-md);
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 0;
  position: relative;
}

/* LOGO */
.logo-link {
  display: flex;
  align-items: center;
  z-index: 1001;
}

.logo-img {
  height: 38px;
  width: auto;
  transition: transform 0.3s ease;
}

.logo-link:hover .logo-img {
  transform: scale(1.05);
}

/* MENÚ HAMBURGUESA */
.menu-toggle {
  display: none;
  flex-direction: column;
  gap: 5px;
  background: none;
  border: none;
  cursor: pointer;
  padding: 8px;
  z-index: 1001;
  transition: transform 0.3s ease;
}

.menu-toggle span {
  display: block;
  width: 25px;
  height: 3px;
  background: var(--primary);
  border-radius: 3px;
  transition: all 0.3s ease;
}

.menu-toggle.active span:nth-child(1) {
  transform: translateY(8px) rotate(45deg);
}

.menu-toggle.active span:nth-child(2) {
  opacity: 0;
}

.menu-toggle.active span:nth-child(3) {
  transform: translateY(-8px) rotate(-45deg);
}

/* NAVEGACIÓN */
.nav {
  display: flex;
  align-items: center;
  gap: 2rem;
}

.nav-link {
  color: var(--text-dark);
  text-decoration: none;
  font-weight: 500;
  font-size: 0.95rem;
  position: relative;
  padding: 0.5rem 0;
  transition: color 0.3s ease;
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 2px;
  background: var(--accent);
  transition: width 0.3s ease;
}

.nav-link:hover {
  color: var(--accent);
}

.nav-link:hover::after {
  width: 100%;
}

.nav-primary {
  background: var(--secondary);
  color: white !important;
  padding: 0.6rem 1.5rem;
  border-radius: 50px;
  font-weight: 600;
  border: 2px solid var(--secondary);
  transition: all 0.3s ease;
}

.nav-primary::after {
  display: none;
}

.nav-primary:hover {
  background: var(--secondary-hover);
  border-color: var(--secondary-hover);
  transform: translateY(-2px);
  box-shadow: var(--shadow-md);
}

/* RESPONSIVE MÓVIL */
@media (max-width: 768px) {
  .menu-toggle {
    display: flex;
  }
  
  .nav {
    position: fixed;
    top: 0;
    right: -100%;
    width: 280px;
    height: 100vh;
    background: var(--bg-white);
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    padding: 5rem 2rem 2rem;
    gap: 1.5rem;
    box-shadow: -5px 0 20px rgba(0,0,0,0.1);
    transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    overflow-y: auto;
  }
  
  .nav.active {
    right: 0;
  }
  
  .nav-link {
    width: 100%;
    padding: 0.75rem 0;
    font-size: 1.1rem;
  }
  
  .nav-link::after {
    display: none;
  }
  
  .nav-primary {
    width: 100%;
    text-align: center;
    padding: 1rem;
  }
  
  /* Overlay cuando menú está abierto */
  .nav-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;
    background: rgba(0,0,0,0.5);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 999;
  }
  
  .nav-overlay.active {
    opacity: 1;
    visibility: visible;
  }
}

/* ============================================
   HERO SECTION
   ============================================ */
.hero {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
  position: relative;
  overflow: hidden;
}

.hero::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: url('data:image/svg+xml,%3Csvg width="60" height="60" xmlns="http://www.w3.org/2000/svg"%3E%3Ccircle cx="30" cy="30" r="1.5" fill="rgba(255,255,255,0.1)"/%3E%3C/svg%3E');
  opacity: 0.4;
}

.hero-content {
  position: relative;
  z-index: 1;
  padding: 3rem 0 2.5rem;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.hero-text {
  color: white;
}

.hero-text h1 {
  color: white;
  margin-bottom: 1rem;
  text-shadow: 0 2px 10px rgba(0,0,0,0.2);
  animation: fadeInUp 0.8s ease;
}

.hero-text p {
  font-size: 1.05rem;
  color: rgba(255,255,255,0.95);
  line-height: 1.7;
  max-width: 600px;
  animation: fadeInUp 1s ease;
}

.hero-small {
  padding: 2.5rem 0 2rem;
}

.hero-small .hero-content {
  padding: 0;
}

/* ============================================
   BUSCADOR
   ============================================ */
.search-box {
  background: var(--bg-white);
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: var(--shadow-xl);
  animation: fadeInUp 1.2s ease;
  transition: all 0.4s ease;
}

.search-box:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

.search-row {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
  margin-bottom: 1rem;
}

@media (min-width: 640px) {
  .search-row {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 768px) {
  .search-row {
    grid-template-columns: repeat(3, 1fr);
  }
}

.field {
  display: flex;
  flex-direction: column;
  gap: 0.4rem;
}

.field label {
  font-size: 0.75rem;
  font-weight: 700;
  color: var(--text-medium);
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.field input,
.field select {
  padding: 0.875rem 1rem;
  border: 2px solid var(--border);
  border-radius: 10px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: var(--bg-light);
  width: 100%;
}

.field input:focus,
.field select:focus {
  outline: none;
  border-color: var(--accent);
  background: white;
  box-shadow: 0 0 0 3px rgba(212,175,55,0.1);
}

.field-button {
  display: flex;
  align-items: flex-end;
}

.btn-primary,
button[type="submit"] {
  background: var(--secondary);
  color: white;
  border: none;
  padding: 1rem 2rem;
  border-radius: 10px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: var(--shadow-md);
  width: 100%;
}

.btn-primary:hover,
button[type="submit"]:hover {
  background: var(--secondary-hover);
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.btn-primary:active,
button[type="submit"]:active {
  transform: translateY(0);
}

/* ============================================
   SECCIONES
   ============================================ */
.how-it-works,
.featured,
.trust,
.results {
  padding: 3rem 0;
}

.how-it-works {
  background: var(--bg-white);
}

.how-it-works h2,
.featured h2,
.trust h2 {
  text-align: center;
  margin-bottom: 2.5rem;
}

/* STEPS */
.steps {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
}

@media (min-width: 640px) {
  .steps {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .steps {
    grid-template-columns: repeat(3, 1fr);
  }
}

.step {
  background: var(--bg-white);
  padding: 2rem 1.5rem;
  border-radius: 12px;
  box-shadow: var(--shadow-sm);
  border: 2px solid var(--border-light);
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}

.step::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--accent) 0%, var(--accent-hover) 100%);
  transform: translateX(-100%);
  transition: transform 0.5s ease;
}

.step:hover {
  transform: translateY(-8px);
  box-shadow: var(--shadow-lg);
  border-color: var(--accent);
}

.step:hover::before {
  transform: translateX(0);
}

.step h3 {
  color: var(--primary);
  margin-bottom: 0.


