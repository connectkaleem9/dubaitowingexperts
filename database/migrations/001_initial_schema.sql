-- 001_initial_schema.sql — Dubai Recovery Experts
-- MySQL 8.0+ / MariaDB 10.6+. Never edit after it has been applied; add a new migration instead.

SET NAMES utf8mb4;

CREATE TABLE admins (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name            VARCHAR(100) NOT NULL,
    email           VARCHAR(191) NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    role            ENUM('owner','editor') NOT NULL DEFAULT 'editor',
    is_active       TINYINT(1) NOT NULL DEFAULT 1,
    failed_logins   SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until    DATETIME NULL,
    last_login_at   DATETIME NULL,
    last_login_ip   VARCHAR(45) NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_admins_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    path            VARCHAR(255) NOT NULL COMMENT 'Relative to public/uploads, without width suffix/extension',
    original_name   VARCHAR(255) NULL,
    mime            VARCHAR(50) NOT NULL DEFAULT 'image/webp',
    width           SMALLINT UNSIGNED NOT NULL,
    height          SMALLINT UNSIGNED NOT NULL,
    widths          VARCHAR(50) NOT NULL COMMENT 'Comma list of generated variant widths',
    size_bytes      INT UNSIGNED NOT NULL DEFAULT 0,
    alt_text        VARCHAR(255) NOT NULL DEFAULT '',
    uploaded_by     INT UNSIGNED NULL,
    created_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_media_path (path),
    KEY idx_media_uploaded_by (uploaded_by),
    CONSTRAINT fk_media_admin FOREIGN KEY (uploaded_by) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE services (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug             VARCHAR(120) NOT NULL,
    name             VARCHAR(120) NOT NULL,
    h1               VARCHAR(160) NOT NULL,
    excerpt          VARCHAR(300) NOT NULL DEFAULT '',
    intro            TEXT NULL,
    body             MEDIUMTEXT NULL COMMENT 'Sanitised HTML',
    icon             VARCHAR(40) NOT NULL DEFAULT 'truck',
    whatsapp_message VARCHAR(300) NULL,
    image_id         INT UNSIGNED NULL,
    sort_order       SMALLINT NOT NULL DEFAULT 0,
    is_published     TINYINT(1) NOT NULL DEFAULT 0,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_services_slug (slug),
    KEY idx_services_pub (is_published, sort_order),
    KEY idx_services_image (image_id),
    CONSTRAINT fk_services_image FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE areas (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug             VARCHAR(120) NOT NULL,
    name             VARCHAR(120) NOT NULL,
    h1               VARCHAR(160) NOT NULL,
    excerpt          VARCHAR(300) NOT NULL DEFAULT '',
    intro            TEXT NULL,
    body             MEDIUMTEXT NULL COMMENT 'Sanitised HTML',
    whatsapp_message VARCHAR(300) NULL,
    image_id         INT UNSIGNED NULL,
    sort_order       SMALLINT NOT NULL DEFAULT 0,
    is_published     TINYINT(1) NOT NULL DEFAULT 0,
    created_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_areas_slug (slug),
    KEY idx_areas_pub (is_published, sort_order),
    KEY idx_areas_image (image_id),
    CONSTRAINT fk_areas_image FOREIGN KEY (image_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE service_area (
    service_id INT UNSIGNED NOT NULL,
    area_id    INT UNSIGNED NOT NULL,
    PRIMARY KEY (service_id, area_id),
    KEY idx_service_area_area (area_id),
    CONSTRAINT fk_sa_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
    CONSTRAINT fk_sa_area    FOREIGN KEY (area_id)    REFERENCES areas(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE projects (
    id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug              VARCHAR(160) NOT NULL,
    title             VARCHAR(160) NOT NULL,
    service_id        INT UNSIGNED NULL,
    area_id           INT UNSIGNED NULL,
    location_text     VARCHAR(160) NULL COMMENT 'Free-text location if no area page',
    vehicle_type      VARCHAR(100) NULL,
    project_date      DATE NULL,
    excerpt           VARCHAR(300) NOT NULL DEFAULT '',
    body              MEDIUMTEXT NULL COMMENT 'Sanitised HTML',
    featured_image_id INT UNSIGNED NULL,
    before_image_id   INT UNSIGNED NULL,
    after_image_id    INT UNSIGNED NULL,
    status            ENUM('draft','published') NOT NULL DEFAULT 'draft',
    published_at      DATETIME NULL,
    created_by        INT UNSIGNED NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_projects_slug (slug),
    KEY idx_projects_status_date (status, project_date),
    KEY idx_projects_service (service_id),
    KEY idx_projects_area (area_id),
    KEY idx_projects_featured (featured_image_id),
    KEY idx_projects_before (before_image_id),
    KEY idx_projects_after (after_image_id),
    KEY idx_projects_created_by (created_by),
    CONSTRAINT fk_projects_service  FOREIGN KEY (service_id)        REFERENCES services(id) ON DELETE SET NULL,
    CONSTRAINT fk_projects_area     FOREIGN KEY (area_id)           REFERENCES areas(id)    ON DELETE SET NULL,
    CONSTRAINT fk_projects_featured FOREIGN KEY (featured_image_id) REFERENCES media(id)    ON DELETE SET NULL,
    CONSTRAINT fk_projects_before   FOREIGN KEY (before_image_id)   REFERENCES media(id)    ON DELETE SET NULL,
    CONSTRAINT fk_projects_after    FOREIGN KEY (after_image_id)    REFERENCES media(id)    ON DELETE SET NULL,
    CONSTRAINT fk_projects_admin    FOREIGN KEY (created_by)        REFERENCES admins(id)   ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE project_images (
    id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    project_id INT UNSIGNED NOT NULL,
    media_id   INT UNSIGNED NOT NULL,
    caption    VARCHAR(255) NOT NULL DEFAULT '',
    sort_order SMALLINT NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY uq_project_media (project_id, media_id),
    KEY idx_project_images_media (media_id),
    CONSTRAINT fk_pi_project FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    CONSTRAINT fk_pi_media   FOREIGN KEY (media_id)   REFERENCES media(id)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reviews (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name         VARCHAR(100) NOT NULL,
    email        VARCHAR(191) NULL,
    phone        VARCHAR(30) NULL,
    service_id   INT UNSIGNED NULL,
    area_text    VARCHAR(120) NULL,
    rating       TINYINT UNSIGNED NOT NULL,
    body         TEXT NOT NULL,
    status       ENUM('pending','approved','rejected','hidden') NOT NULL DEFAULT 'pending',
    is_featured  TINYINT(1) NOT NULL DEFAULT 0,
    consent_at   DATETIME NOT NULL,
    ip_hash      CHAR(64) NULL,
    admin_note   VARCHAR(500) NULL,
    approved_at  DATETIME NULL,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_reviews_status (status, is_featured, approved_at),
    KEY idx_reviews_service (service_id),
    CONSTRAINT fk_reviews_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE review_images (
    id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
    review_id INT UNSIGNED NOT NULL,
    media_id  INT UNSIGNED NOT NULL,
    PRIMARY KEY (id),
    KEY idx_ri_review (review_id),
    KEY idx_ri_media (media_id),
    CONSTRAINT fk_ri_review FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    CONSTRAINT fk_ri_media  FOREIGN KEY (media_id)  REFERENCES media(id)   ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE contact_submissions (
    id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name              VARCHAR(100) NOT NULL,
    phone             VARCHAR(30) NOT NULL,
    email             VARCHAR(191) NULL,
    service_id        INT UNSIGNED NULL,
    service_text      VARCHAR(120) NULL,
    location          VARCHAR(200) NOT NULL,
    vehicle_type      VARCHAR(100) NULL,
    message           TEXT NULL,
    preferred_contact ENUM('phone','whatsapp','email') NOT NULL DEFAULT 'phone',
    status            ENUM('new','contacted','in_progress','completed','cancelled') NOT NULL DEFAULT 'new',
    form_type         ENUM('contact','quote','landing') NOT NULL DEFAULT 'contact',
    source_path       VARCHAR(255) NULL,
    utm_source        VARCHAR(100) NULL,
    utm_medium        VARCHAR(100) NULL,
    utm_campaign      VARCHAR(150) NULL,
    utm_term          VARCHAR(150) NULL,
    gclid             VARCHAR(255) NULL,
    ip_hash           CHAR(64) NULL,
    user_agent        VARCHAR(255) NULL,
    admin_notes       TEXT NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_leads_status_date (status, created_at),
    KEY idx_leads_created (created_at),
    KEY idx_leads_service (service_id),
    CONSTRAINT fk_leads_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE faqs (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    question     VARCHAR(255) NOT NULL,
    answer       TEXT NOT NULL COMMENT 'Sanitised HTML',
    category     VARCHAR(60) NOT NULL DEFAULT 'General',
    service_id   INT UNSIGNED NULL,
    area_id      INT UNSIGNED NULL,
    show_on_home TINYINT(1) NOT NULL DEFAULT 0,
    sort_order   SMALLINT NOT NULL DEFAULT 0,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_faqs_pub (is_published, category, sort_order),
    KEY idx_faqs_service (service_id),
    KEY idx_faqs_area (area_id),
    CONSTRAINT fk_faqs_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
    CONSTRAINT fk_faqs_area    FOREIGN KEY (area_id)    REFERENCES areas(id)    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE posts (
    id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
    slug              VARCHAR(160) NOT NULL,
    title             VARCHAR(160) NOT NULL,
    excerpt           VARCHAR(300) NOT NULL DEFAULT '',
    body              MEDIUMTEXT NOT NULL COMMENT 'Sanitised HTML',
    featured_image_id INT UNSIGNED NULL,
    status            ENUM('draft','published') NOT NULL DEFAULT 'draft',
    published_at      DATETIME NULL,
    author_id         INT UNSIGNED NULL,
    created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_posts_slug (slug),
    KEY idx_posts_status_date (status, published_at),
    KEY idx_posts_image (featured_image_id),
    KEY idx_posts_author (author_id),
    CONSTRAINT fk_posts_image  FOREIGN KEY (featured_image_id) REFERENCES media(id)  ON DELETE SET NULL,
    CONSTRAINT fk_posts_author FOREIGN KEY (author_id)         REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE seo_metadata (
    id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    path             VARCHAR(255) NOT NULL COMMENT 'Site path with trailing slash, e.g. /services/car-recovery/',
    title            VARCHAR(70) NULL,
    meta_description VARCHAR(170) NULL,
    og_image_id      INT UNSIGNED NULL,
    robots           VARCHAR(40) NULL COMMENT 'e.g. noindex,follow; NULL = default',
    updated_at       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_seo_path (path),
    KEY idx_seo_og (og_image_id),
    CONSTRAINT fk_seo_og FOREIGN KEY (og_image_id) REFERENCES media(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE settings (
    `key`      VARCHAR(80) NOT NULL,
    `value`    TEXT NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE activity_logs (
    id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    admin_id    INT UNSIGNED NULL,
    action      VARCHAR(60) NOT NULL,
    entity_type VARCHAR(40) NULL,
    entity_id   INT UNSIGNED NULL,
    details     VARCHAR(500) NULL,
    ip          VARCHAR(45) NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_logs_created (created_at),
    KEY idx_logs_admin (admin_id),
    KEY idx_logs_entity (entity_type, entity_id),
    CONSTRAINT fk_logs_admin FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rate_limits (
    bucket     VARCHAR(191) NOT NULL,
    hits       INT UNSIGNED NOT NULL DEFAULT 0,
    reset_at   DATETIME NOT NULL,
    PRIMARY KEY (bucket),
    KEY idx_rate_reset (reset_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
