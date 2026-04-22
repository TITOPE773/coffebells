-- ============================================================
-- SCHEMA.SQL — CoffeeBells & Home
-- Ejecutar en phpMyAdmin o con: mysql -u root coffeebells < schema.sql
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ── BASE DE DATOS ─────────────────────────────────────────
CREATE DATABASE IF NOT EXISTS coffeebells
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE coffeebells;

-- ── USUARIOS ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120)  NOT NULL,
    email       VARCHAR(180)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    phone       VARCHAR(20)   DEFAULT NULL,
    role        ENUM('admin','editor','customer') DEFAULT 'customer',
    active      TINYINT(1) DEFAULT 1,
    last_login  DATETIME      DEFAULT NULL,
    created_at  DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Admin por defecto: admin@coffeebells.mx / Admin2024!
INSERT INTO users (name, email, password, role) VALUES
('Administrador', 'admin@coffeebells.mx',
 '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ── CATEGORÍAS DE PRODUCTOS ───────────────────────────────
CREATE TABLE IF NOT EXISTS product_categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)  NOT NULL,
    slug        VARCHAR(120)  NOT NULL UNIQUE,
    description TEXT          DEFAULT NULL,
    icon        VARCHAR(50)   DEFAULT 'tag',
    image       VARCHAR(255)  DEFAULT NULL,
    active      TINYINT(1)    DEFAULT 1,
    sort_order  INT           DEFAULT 0
) ENGINE=InnoDB;

INSERT INTO product_categories (name, slug, icon) VALUES
('Café',        'cafe',        'cup-hot'),
('Jardinería',  'jardineria',  'tree'),
('Decoración',  'decoracion',  'house-heart'),
('Iluminación', 'iluminacion', 'lightbulb'),
('Accesorios',  'accesorios',  'bag');

-- ── PRODUCTOS ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS products (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name                VARCHAR(200) NOT NULL,
    slug                VARCHAR(220) NOT NULL UNIQUE,
    short_description   VARCHAR(300) DEFAULT NULL,
    description         TEXT         DEFAULT NULL,
    price               DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    price_old           DECIMAL(10,2) DEFAULT NULL,
    stock               INT          DEFAULT NULL COMMENT 'NULL = ilimitado',
    sku                 VARCHAR(60)  DEFAULT NULL,
    image               VARCHAR(255) DEFAULT NULL,
    badge               ENUM('new','sale','hot','') DEFAULT '',
    featured            TINYINT(1)   DEFAULT 0,
    active              TINYINT(1)   DEFAULT 1,
    sales_count         INT UNSIGNED DEFAULT 0,
    category_id         INT UNSIGNED DEFAULT NULL,
    created_at          DATETIME     DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── IMÁGENES ADICIONALES ──────────────────────────────────
CREATE TABLE IF NOT EXISTS product_images (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id  INT UNSIGNED NOT NULL,
    image       VARCHAR(255) NOT NULL,
    sort_order  INT          DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── PEDIDOS ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS orders (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    folio            VARCHAR(30)   NOT NULL UNIQUE,
    customer_name    VARCHAR(120)  NOT NULL,
    customer_email   VARCHAR(180)  DEFAULT NULL,
    customer_phone   VARCHAR(20)   NOT NULL,
    customer_address TEXT          DEFAULT NULL,
    customer_city    VARCHAR(100)  DEFAULT NULL,
    subtotal         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    shipping         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total            DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    payment_method   VARCHAR(50)   DEFAULT 'efectivo',
    payment_status   ENUM('pending','paid','failed') DEFAULT 'pending',
    status           ENUM('pending','confirmed','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    coupon_code      VARCHAR(30)   DEFAULT NULL,
    notes            TEXT          DEFAULT NULL,
    user_id          INT UNSIGNED  DEFAULT NULL,
    created_at       DATETIME      DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── ÍTEMS DE PEDIDO ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS order_items (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id     INT UNSIGNED  NOT NULL,
    product_id   INT UNSIGNED  DEFAULT NULL,
    product_name VARCHAR(200)  NOT NULL,
    price        DECIMAL(10,2) NOT NULL,
    qty          INT UNSIGNED  NOT NULL DEFAULT 1,
    subtotal     DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id)   REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── CONTACTOS / LEADS ─────────────────────────────────────
CREATE TABLE IF NOT EXISTS contacts (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(120)  NOT NULL,
    email        VARCHAR(180)  DEFAULT NULL,
    phone        VARCHAR(20)   DEFAULT NULL,
    subject      VARCHAR(200)  DEFAULT NULL,
    service      VARCHAR(150)  DEFAULT NULL,
    message      TEXT          DEFAULT NULL,
    budget       VARCHAR(50)   DEFAULT NULL,
    contact_pref VARCHAR(50)   DEFAULT NULL,
    source       VARCHAR(50)   DEFAULT 'web_form',
    status       ENUM('new','read','contacted','quoted','closed','lost') DEFAULT 'new',
    created_at   DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── NEWSLETTER ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email      VARCHAR(180)  NOT NULL UNIQUE,
    name       VARCHAR(120)  DEFAULT NULL,
    source     VARCHAR(50)   DEFAULT 'footer',
    active     TINYINT(1)    DEFAULT 1,
    created_at DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── CUPONES ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS coupons (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code         VARCHAR(30)   NOT NULL UNIQUE,
    type         ENUM('percent','fixed') DEFAULT 'percent',
    value        DECIMAL(10,2) NOT NULL,
    min_order    DECIMAL(10,2) DEFAULT 0,
    uses_max     INT           DEFAULT NULL COMMENT 'NULL = ilimitado',
    uses_count   INT           DEFAULT 0,
    expires_at   DATETIME      DEFAULT NULL,
    active       TINYINT(1)    DEFAULT 1,
    created_at   DATETIME      DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO coupons (code, type, value, min_order, expires_at) VALUES
('BIENVENIDO10', 'percent', 10.00, 200.00, DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('CAFE50',       'fixed',   50.00, 300.00, DATE_ADD(NOW(), INTERVAL 6 MONTH));

-- ── SERVICIOS ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS service_categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(120) NOT NULL UNIQUE,
    icon        VARCHAR(50)  DEFAULT 'tools',
    description TEXT         DEFAULT NULL,
    active      TINYINT(1)   DEFAULT 1,
    sort_order  INT          DEFAULT 0
) ENGINE=InnoDB;

INSERT INTO service_categories (name, slug, icon) VALUES
('Electricidad',  'electricidad',  'lightning-charge'),
('Decoración',    'decoracion',    'house-heart'),
('Jardinería',    'jardineria',    'tree'),
('Coffee Bells',  'cafe',          'cup-hot');

CREATE TABLE IF NOT EXISTS services (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name              VARCHAR(200) NOT NULL,
    slug              VARCHAR(220) NOT NULL UNIQUE,
    short_description VARCHAR(300) DEFAULT NULL,
    description       TEXT         DEFAULT NULL,
    icon              VARCHAR(50)  DEFAULT 'tools',
    image             VARCHAR(255) DEFAULT NULL,
    price_from        DECIMAL(10,2) DEFAULT NULL,
    featured          TINYINT(1)   DEFAULT 0,
    active            TINYINT(1)   DEFAULT 1,
    sort_order        INT          DEFAULT 0,
    category_id       INT UNSIGNED DEFAULT NULL,
    created_at        DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── BLOG ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS blog_categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    slug        VARCHAR(120) NOT NULL UNIQUE,
    active      TINYINT(1)   DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO blog_categories (name, slug) VALUES
('Electricidad', 'electricidad'),
('Decoración',   'decoracion'),
('Jardinería',   'jardineria'),
('Café',         'cafe'),
('Tutoriales',   'tutoriales');

CREATE TABLE IF NOT EXISTS blog_posts (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(255) NOT NULL,
    slug         VARCHAR(280) NOT NULL UNIQUE,
    excerpt      TEXT         DEFAULT NULL,
    content      LONGTEXT     DEFAULT NULL,
    image        VARCHAR(255) DEFAULT NULL,
    featured     TINYINT(1)   DEFAULT 0,
    published    TINYINT(1)   DEFAULT 0,
    views        INT UNSIGNED DEFAULT 0,
    category_id  INT UNSIGNED DEFAULT NULL,
    author_id    INT UNSIGNED DEFAULT NULL,
    published_at DATETIME     DEFAULT NULL,
    created_at   DATETIME     DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id)   REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── TESTIMONIOS ───────────────────────────────────────────
CREATE TABLE IF NOT EXISTS testimonials (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL,
    role       VARCHAR(100) DEFAULT NULL,
    message    TEXT         NOT NULL,
    rating     TINYINT      DEFAULT 5,
    image      VARCHAR(255) DEFAULT NULL,
    service    VARCHAR(150) DEFAULT NULL,
    featured   TINYINT(1)   DEFAULT 0,
    active     TINYINT(1)   DEFAULT 1,
    created_at DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO testimonials (name, role, message, rating, service, featured, active) VALUES
('Laura Martínez',   'Ama de casa, Salamanca',    'Transformaron completamente mi sala con la iluminación LED. El trabajo fue impecable y rápido. ¡100% recomendado!', 5, 'Iluminación', 1, 1),
('Carlos Rodríguez', 'Arquitecto',                'Contratamos sus servicios para un proyecto residencial completo. Excelente calidad y profesionalismo en cada detalle.', 5, 'Instalación eléctrica', 1, 1),
('Ana Pérez',        'Emprendedora',              'El café de especialidad es extraordinario. Cada vez que visito su espacio, el aroma y sabor me transportan.', 5, 'Coffee Bells', 1, 1),
('Roberto Silva',    'Propietario de negocio',    'El jardín de mi empresa quedó increíble. El diseño es moderno, el mantenimiento es puntual y los precios son justos.', 5, 'Jardinería', 1, 1),
('María González',   'Diseñadora de interiores',  'Trabajo con ellos como partner en proyectos de decoración. La calidad y puntualidad son excepcionales.', 5, 'Decoración', 1, 1),
('Jorge Herrera',    'Padre de familia',          'Instalaron todo el sistema eléctrico de mi casa nueva. Trabajo limpio, garantía real y precio muy competitivo.', 5, 'Electricidad', 1, 1);

-- ── FAQs ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS faqs (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question   VARCHAR(300) NOT NULL,
    answer     TEXT         NOT NULL,
    category   VARCHAR(100) DEFAULT 'general',
    sort_order INT          DEFAULT 0,
    active     TINYINT(1)   DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO faqs (question, answer, category, sort_order) VALUES
('¿Hacen cotizaciones gratis?',             'Sí, todas nuestras cotizaciones son completamente gratuitas y sin compromiso. Puedes solicitarla por WhatsApp, teléfono o formulario.',                             'general',      1),
('¿En qué zonas trabajan?',                  'Atendemos toda el área metropolitana de Salamanca, Guanajuato y municipios cercanos. Para proyectos fuera de la zona, consúltanos.',                             'general',      2),
('¿Tienen garantía en sus servicios?',       'Sí. Todos nuestros servicios incluyen garantía de mano de obra. Los plazos varían según el servicio: electricidad 6 meses, jardinería 3 meses.',                 'servicios',    3),
('¿Cuánto tiempo tardan en responder?',     'Respondemos WhatsApp y llamadas en menos de 2 horas en horario de atención. Correos en máximo 4 horas.',                                                          'general',      4),
('¿Aceptan pagos en línea?',                 'Sí. Aceptamos transferencia bancaria, tarjeta de crédito/débito, efectivo y pago por WhatsApp. El detalle lo ves en el checkout.',                               'tienda',       5),
('¿Tienen envíos a toda la república?',     'Sí. Realizamos envíos a todo México vía paquetería. Envío gratis en pedidos mayores a $800 MXN.',                                                                 'tienda',       6),
('¿Puedo pedir café de especialidad online?','Claro, toda nuestra línea de café está disponible en la tienda online con envío a todo México.',                                                                   'cafe',         7),
('¿Ofrecen planes de mantenimiento?',       'Sí, contamos con planes mensuales y trimestrales de mantenimiento eléctrico, de jardín e iluminación. Pregunta por nuestros paquetes.',                           'servicios',    8);

-- ── GALERÍA ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS gallery (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200) DEFAULT NULL,
    image       VARCHAR(255) NOT NULL,
    category    VARCHAR(100) DEFAULT 'general',
    sort_order  INT          DEFAULT 0,
    active      TINYINT(1)   DEFAULT 1,
    created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── CONFIGURACIÓN DEL SITIO ───────────────────────────────
CREATE TABLE IF NOT EXISTS site_config (
    cfg_key    VARCHAR(80)  NOT NULL PRIMARY KEY,
    cfg_value  TEXT         DEFAULT NULL,
    updated_at DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO site_config (cfg_key, cfg_value) VALUES
('site_name',                  'CoffeeBells & Home'),
('site_tagline',               'Iluminamos tu hogar, despertamos tus sentidos'),
('site_phone',                 '+52 464 123 4567'),
('site_whatsapp',              '524641234567'),
('site_email',                 'hola@coffeebells.mx'),
('site_address',               'Salamanca, Guanajuato, México'),
('site_city',                  'Salamanca, Guanajuato, MX'),
('hours_week',                 '9:00 – 20:00'),
('hours_weekend',              '10:00 – 15:00'),
('fb_url',                     'https://facebook.com/coffeebells'),
('ig_url',                     'https://instagram.com/coffeebells'),
('tiktok_url',                 'https://tiktok.com/@coffeebells'),
('yt_url',                     ''),
('free_shipping_min',          '800'),
('shipping_cost',              '99'),
('popup_enabled',              '1'),
('popup_title',                '¡Bienvenido! Obtén 10% OFF en tu primer pedido'),
('popup_discount',             '10'),
('meta_description',           'CoffeeBells & Home — Electricidad, decoración, jardinería y café de especialidad en Salamanca, Guanajuato. Cotización gratis.'),
('admin_email_notifications',  'admin@coffeebells.mx');

SET FOREIGN_KEY_CHECKS = 1;