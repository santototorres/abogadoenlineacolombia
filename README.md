# Abogado en Línea Colombia

## Descripción

Plataforma web para conectar clientes con abogados en Colombia. Sistema de directorio que permite a los usuarios buscar, comparar y contactar profesionales del derecho según su especialidad, ubicación y experiencia.

## Características Principales

### Para Clientes
- 🔍 **Búsqueda Avanzada**: Encuentra abogados por especialidad, ciudad y nombre
- ⭐ **Sistema de Reseñas**: Consulta opiniones y calificaciones de otros clientes
- 💬 **Mensajería Directa**: Comunícate con los abogados de forma privada
- 📱 **Diseño Responsivo**: Acceso desde cualquier dispositivo (móvil, tablet, desktop)

### Para Abogados
- 👤 **Perfil Profesional**: Crea y personaliza tu perfil con experiencia y especialidades
- 📊 **Panel de Control**: Gestiona tus mensajes y consultas
- 🔔 **Notificaciones**: Recibe alertas de nuevas consultas

## Tecnologías Utilizadas

- **Backend**: PHP 7.4+
- **Frontend**: HTML5, CSS3, JavaScript
- **Base de Datos**: MySQL
- **Diseño**: CSS Custom Properties, Flexbox, Grid
- **Responsive Design**: Mobile-first approach

## Estructura del Proyecto

```
abogadoenlineacolombia/
├── config/              # Archivos de configuración
├── images/              # Imágenes y recursos gráficos
├── uploads/             # Archivos subidos por usuarios
├── index.php            # Página principal
├── header.php           # Encabezado del sitio
├── styles.css           # Estilos principales
├── lawyer_profile.php   # Perfil de abogado
├── results.php          # Resultados de búsqueda
├── login.php            # Inicio de sesión
├── register_client.php  # Registro de clientes
├── register_lawyer.php  # Registro de abogados
├── messages.php         # Sistema de mensajería
└── add_review.php       # Agregar reseñas
```

## Instalación

### Requisitos Previos
- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx)

### Pasos de Instalación

1. Clonar el repositorio:
```bash
git clone https://github.com/santototorres/abogadoenlineacolombia.git
cd abogadoenlineacolombia
```

2. Configurar la base de datos:
- Crear una base de datos MySQL
- Importar el esquema de base de datos
- Actualizar credenciales en `config/database.php`

3. Configurar el servidor:
- Apuntar el document root a la carpeta del proyecto
- Asegurar permisos de escritura en la carpeta `uploads/`

4. Acceder a la aplicación:
```
http://localhost/abogadoenlineacolombia
```

## Uso

### Como Cliente
1. Registrarse o iniciar sesión
2. Utilizar el buscador para encontrar abogados
3. Revisar perfiles y reseñas
4. Contactar al abogado mediante mensajería
5. Dejar una reseña después del servicio

### Como Abogado
1. Registrarse con datos profesionales
2. Completar perfil con especialidades y experiencia
3. Responder mensajes de clientes potenciales
4. Mantener perfil actualizado

## Características de Diseño

### Diseño Responsivo
- **Mobile**: < 768px - Menú hamburguesa, diseño en columna
- **Tablet**: 768px - 1024px - Grid de 2 columnas
- **Desktop**: > 1024px - Grid de 3 columnas, navegación completa

### Paleta de Colores
- **Primario**: #1e3a5f (Azul profesional)
- **Acento**: #d4af37 (Dorado)
- **Secundario**: #e74c3c (Rojo para llamados a la acción)

## Seguridad

- Validación de datos en cliente y servidor
- Protección contra inyección SQL
- Sesiones seguras con PHP
- Sanitización de inputs de usuario

## Contribuir

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu característica (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## Licencia

Este proyecto está bajo una licencia privada. Contactar al autor para más información.

## Contacto

**Desarrollador**: santototorres
**GitHub**: [@santototorres](https://github.com/santototorres)

## Estado del Proyecto

✅ **Versión Actual**: 1.0
🚀 **Estado**: En desarrollo activo
📅 **Última actualización**: 2025

---

*Conectando abogados y clientes en Colombia*
