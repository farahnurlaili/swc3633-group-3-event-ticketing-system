-- =====================================================================
-- Event Ticketing and Venue Booking System  |  DDL (schema.sql)
-- 6 tables: users, venues, categories, events, bookings, payments
-- Works on MySQL 8.0.16+ and MariaDB 10.2+ (XAMPP / phpMyAdmin)
-- =====================================================================

DROP DATABASE IF EXISTS event_ticketing;
CREATE DATABASE event_ticketing
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE event_ticketing;

-- ---------------------------------------------------------------------
-- 1. USERS  (admin / organiser / customer)            [MAIN]
-- ---------------------------------------------------------------------
CREATE TABLE users (
  user_id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name     VARCHAR(100) NOT NULL,
  email         VARCHAR(100) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  phone         VARCHAR(20)  NULL,
  role          ENUM('admin','organiser','customer') NOT NULL DEFAULT 'customer',
  created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uq_users_email UNIQUE (email)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 2. VENUES                                           [MAIN]
-- ---------------------------------------------------------------------
CREATE TABLE venues (
  venue_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  venue_name  VARCHAR(100) NOT NULL,
  address     VARCHAR(255) NOT NULL,
  city        VARCHAR(60)  NOT NULL,
  capacity    INT UNSIGNED NOT NULL,
  created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uq_venues_name_city UNIQUE (venue_name, city),
  CONSTRAINT chk_venues_capacity CHECK (capacity > 0)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 3. CATEGORIES  (event categories)                   [ADDITIONAL]
-- ---------------------------------------------------------------------
CREATE TABLE categories (
  category_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(50)  NOT NULL,
  description   VARCHAR(255) NULL,
  CONSTRAINT uq_categories_name UNIQUE (category_name)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 4. EVENTS  (FK -> venues, categories, users[organiser])   [MAIN]
--    Schedule and ticket availability are stored on the event itself.
-- ---------------------------------------------------------------------
CREATE TABLE events (
  event_id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title             VARCHAR(150) NOT NULL,
  description       TEXT NULL,
  venue_id          INT UNSIGNED NOT NULL,
  category_id       INT UNSIGNED NOT NULL,
  organiser_id      INT UNSIGNED NOT NULL,
  event_date        DATE NOT NULL,
  start_time        TIME NOT NULL,
  end_time          TIME NOT NULL,
  ticket_price      DECIMAL(10,2) NOT NULL,
  total_tickets     INT UNSIGNED NOT NULL,
  available_tickets INT UNSIGNED NOT NULL,
  status            ENUM('draft','published','cancelled') NOT NULL DEFAULT 'draft',
  created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_events_venue
    FOREIGN KEY (venue_id)     REFERENCES venues(venue_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_events_category
    FOREIGN KEY (category_id)  REFERENCES categories(category_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_events_organiser
    FOREIGN KEY (organiser_id) REFERENCES users(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_events_time    CHECK (end_time > start_time),
  CONSTRAINT chk_events_price   CHECK (ticket_price >= 0),
  CONSTRAINT chk_events_total   CHECK (total_tickets > 0),
  CONSTRAINT chk_events_avail   CHECK (available_tickets <= total_tickets),
  CONSTRAINT uq_events_venue_slot UNIQUE (venue_id, event_date, start_time),
  INDEX idx_events_status (status),
  INDEX idx_events_date (event_date)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 5. BOOKINGS  (FK -> users, events)                  [MAIN]
-- ---------------------------------------------------------------------
CREATE TABLE bookings (
  booking_id  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED NOT NULL,
  event_id    INT UNSIGNED NOT NULL,
  quantity    INT UNSIGNED NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  status      ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
  booked_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_bookings_user
    FOREIGN KEY (user_id)  REFERENCES users(user_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT fk_bookings_event
    FOREIGN KEY (event_id) REFERENCES events(event_id)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT chk_bookings_qty   CHECK (quantity > 0),
  CONSTRAINT chk_bookings_total CHECK (total_price >= 0),
  INDEX idx_bookings_user (user_id),
  INDEX idx_bookings_event (event_id),
  INDEX idx_bookings_status (status)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- 6. PAYMENTS  (one payment record per booking)       [ADDITIONAL]
-- ---------------------------------------------------------------------
CREATE TABLE payments (
  payment_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  booking_id      INT UNSIGNED NOT NULL,
  amount          DECIMAL(10,2) NOT NULL,
  method          ENUM('card','online_banking','ewallet','cash') NOT NULL,
  status          ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  transaction_ref VARCHAR(50) NULL,
  paid_at         DATETIME NULL,
  CONSTRAINT fk_payments_booking
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT uq_payments_booking UNIQUE (booking_id),
  CONSTRAINT chk_payments_amount CHECK (amount >= 0)
) ENGINE=InnoDB;
