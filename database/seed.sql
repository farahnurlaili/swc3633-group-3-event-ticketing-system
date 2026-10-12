-- =====================================================================
-- Event Ticketing and Venue Booking System  |  DML (seed.sql)
-- Run AFTER schema.sql. Insert order respects foreign keys.
-- All sample users share the password:  Password123!  (bcrypt hashed)
-- =====================================================================

USE event_ticketing;

-- ---------------------------------------------------------------------
-- USERS (7)
-- ---------------------------------------------------------------------
INSERT INTO users (user_id, full_name, email, password_hash, phone, role) VALUES
(1, 'Aisyah Rahman',  'admin@eventhub.my',    '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '012-3456789', 'admin'),
(2, 'Daniel Lim',     'daniel@eventhub.my',   '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '013-1112233', 'organiser'),
(3, 'Nurul Huda',     'nurul@eventhub.my',    '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '014-5556677', 'organiser'),
(4, 'Farid Hakim',    'farid@example.com',    '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '011-2345678', 'customer'),
(5, 'Tan Mei Ling',   'meiling@example.com',  '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '016-7778899', 'customer'),
(6, 'Arun Kumar',     'arun@example.com',     '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '017-3334455', 'customer'),
(7, 'Siti Aminah',    'siti@example.com',     '$2b$10$TSPAb5uLRvDB3USHAoScyuIG3veGglRDMIk5YJ/9Bktqc27/l8.2G', '019-8889900', 'customer');

-- ---------------------------------------------------------------------
-- VENUES (5)
-- ---------------------------------------------------------------------
INSERT INTO venues (venue_id, venue_name, address, city, capacity) VALUES
(1, 'Arena Seri Nusantara',      '1 Jalan Stadium',        'Kuala Lumpur', 15000),
(2, 'Pusat Konvensyen Harmoni',  '88 Jalan Persidangan',   'Kuala Lumpur', 3000),
(3, 'Kompleks Sukan Bestari',    '12 Jalan Sukan',         'Shah Alam',    2500),
(4, 'Panggung Budaya Warisan',   '5 Jalan Seni',           'George Town',  800),
(5, 'Dewan Komuniti Gemilang',   '23 Lorong Gemilang',     'Johor Bahru',  400);

-- ---------------------------------------------------------------------
-- CATEGORIES (5)
-- ---------------------------------------------------------------------
INSERT INTO categories (category_id, category_name, description) VALUES
(1, 'Concert',   'Live music performances and festivals'),
(2, 'Seminar',   'Talks, conferences and professional workshops'),
(3, 'Sports',    'Tournaments and sporting events'),
(4, 'Arts',      'Cultural shows, theatre and exhibitions'),
(5, 'Comedy',    'Stand-up and live comedy shows');

-- ---------------------------------------------------------------------
-- EVENTS (6)  available_tickets already reflects the bookings below
-- (pending and confirmed bookings hold tickets; cancelled ones do not)
-- ---------------------------------------------------------------------
INSERT INTO events (event_id, title, description, venue_id, category_id, organiser_id,
                    event_date, start_time, end_time, ticket_price, total_tickets, available_tickets, status) VALUES
(1, 'Rock Fest 2026',         'Outdoor rock festival with local and regional bands.',            1, 1, 2, '2026-12-12', '19:00:00', '23:00:00', 150.00, 500, 494, 'published'),
(2, 'Tech Innovators Summit', 'One-day conference on AI, cloud and software engineering.',       2, 2, 2, '2026-11-20', '09:00:00', '17:00:00',  80.00, 200, 197, 'published'),
(3, 'Charity Futsal Cup',     'Inter-university futsal tournament in aid of local charities.',   3, 3, 3, '2026-11-28', '10:00:00', '18:00:00',  25.00, 300, 296, 'published'),
(4, 'Festival Seni Warisan',  'Traditional dance, music and craft showcase.',                    4, 4, 3, '2026-12-05', '18:00:00', '22:00:00',  40.00, 250, 250, 'published'),
(5, 'Stand-Up Comedy Night',  'An evening of stand-up with five up-and-coming comedians.',       5, 5, 2, '2026-11-15', '20:00:00', '22:30:00',  60.00, 120, 118, 'published'),
(6, 'Startup Pitch Day',      'Early-stage startups pitch to investors. Details to be finalised.', 2, 2, 3, '2027-01-18', '09:30:00', '16:00:00', 20.00, 100, 100, 'draft');

-- ---------------------------------------------------------------------
-- BOOKINGS (7)
-- ---------------------------------------------------------------------
INSERT INTO bookings (booking_id, user_id, event_id, quantity, total_price, status) VALUES
(1, 4, 1, 2, 300.00, 'confirmed'),
(2, 5, 1, 3, 450.00, 'confirmed'),
(3, 6, 2, 3, 240.00, 'confirmed'),
(4, 4, 3, 4, 100.00, 'pending'),
(5, 5, 4, 2,  80.00, 'cancelled'),
(6, 7, 5, 2, 120.00, 'confirmed'),
(7, 6, 1, 1, 150.00, 'confirmed');

-- ---------------------------------------------------------------------
-- PAYMENTS (7)
-- ---------------------------------------------------------------------
INSERT INTO payments (payment_id, booking_id, amount, method, status, transaction_ref, paid_at) VALUES
(1, 1, 300.00, 'card',           'paid',     'TXN-20261001-0001', '2026-10-01 10:15:00'),
(2, 2, 450.00, 'online_banking', 'paid',     'TXN-20261002-0002', '2026-10-02 14:30:00'),
(3, 3, 240.00, 'ewallet',        'paid',     'TXN-20261003-0003', '2026-10-03 09:05:00'),
(4, 4, 100.00, 'online_banking', 'pending',  'TXN-20261004-0004', NULL),
(5, 5,  80.00, 'card',           'refunded', 'TXN-20261004-0005', '2026-10-04 16:45:00'),
(6, 6, 120.00, 'ewallet',        'paid',     'TXN-20261005-0006', '2026-10-05 11:20:00'),
(7, 7, 150.00, 'card',           'paid',     'TXN-20261006-0007', '2026-10-06 19:40:00');

-- =====================================================================
-- SAMPLE DML QUERIES (SELECT / UPDATE / DELETE) -- for the report.
-- Uncomment and run one at a time to take screenshots.
-- =====================================================================

-- SELECT: published events with venue, category and organiser
-- SELECT e.event_id, e.title, e.event_date, e.ticket_price, e.available_tickets,
--        v.venue_name, c.category_name, u.full_name AS organiser
-- FROM events e
-- JOIN venues v     ON e.venue_id = v.venue_id
-- JOIN categories c ON e.category_id = c.category_id
-- JOIN users u      ON e.organiser_id = u.user_id
-- WHERE e.status = 'published'
-- ORDER BY e.event_date;

-- SELECT: one customer's bookings with event and payment status
-- SELECT b.booking_id, e.title, b.quantity, b.total_price, b.status AS booking_status,
--        p.status AS payment_status
-- FROM bookings b
-- JOIN events e        ON b.event_id = e.event_id
-- LEFT JOIN payments p ON p.booking_id = b.booking_id
-- WHERE b.user_id = 4;

-- UPDATE: confirm a pending booking
-- UPDATE bookings SET status = 'confirmed' WHERE booking_id = 4;

-- UPDATE: cancel a booking and give the tickets back
-- UPDATE bookings SET status = 'cancelled' WHERE booking_id = 7;
-- UPDATE events SET available_tickets = available_tickets + 1 WHERE event_id = 1;

-- DELETE: remove a draft event (no bookings reference it)
-- DELETE FROM events WHERE event_id = 6;
