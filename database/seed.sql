-- ============================================================
--  PharmaTrack - Online Pharmacy Stock Alert System
--  seed.sql - demo/test data
--
--  Demo passwords:
--    admin  / admin123
--    remy   / remy123
--    claire / claire123
-- ============================================================

USE pharmacy_stock_alert;

SET NAMES utf8mb4;

-- ------------------------------------------------------------
-- Users (bcrypt hashes produced with password_hash)
-- ------------------------------------------------------------
INSERT INTO users (username, email, password_hash, role) VALUES
('admin',  'admin@pharmatrack.rw',  '$2y$10$d.zgj26oxlRxoMAGv1Lg0.0ONJk76Hl8fRkiHs9QaGYlqaK5hMfDG', 'admin'),
('remy',   'remy@pharmatrack.rw',   '$2y$10$2v9YsNcMe1UODEIBty81ROeMgD..ZVVB/WziEN7LhvrMVHcBqAqLu', 'pharmacist'),
('claire', 'claire@pharmatrack.rw', '$2y$10$DHQHb.CPzdVFyWM8rTqkXeIUhPxy4M6Y2N9AyId0RE58ZXQ0e.NZm', 'pharmacist');

-- ------------------------------------------------------------
-- Suppliers
-- ------------------------------------------------------------
INSERT INTO suppliers (supplier_id, name, phone, email, address) VALUES
(1, 'Kigali Pharma Distributors', '+250 788 123 456', 'sales@kigalipharma.rw',  'KG 11 Ave, Remera, Kigali'),
(2, 'Rwanda Medical Supply',      '+250 788 654 321', 'orders@rms.rw',          'KN 3 Rd, Nyarugenge, Kigali'),
(3, 'East Africa Drug House',     '+250 722 987 654', 'info@eadh.co.rw',        'KK 15 Rd, Kicukiro, Kigali'),
(4, 'Huye Health Supplies',       '+250 733 456 789', 'contact@huyehealth.rw',  'Ngoma Sector, Huye');

-- ------------------------------------------------------------
-- Medicines (mix of healthy, low, expiring and expired)
-- ------------------------------------------------------------
INSERT INTO medicines (medicine_id, name, category, stock, reorder_level, expiry_date, supplier_id) VALUES
(1,  'Paracetamol 500mg',              'Analgesics',        120, 50,  '2027-08-31', 1),
(2,  'Amoxicillin 500mg',              'Antibiotics',        18, 40,  '2027-02-28', 2),
(3,  'Artemether/Lumefantrine 20/120', 'Antimalarials',      75, 30,  '2026-12-15', 2),
(4,  'Metformin 500mg',                'Diabetes',            8, 25,  '2028-01-31', 3),
(5,  'Ibuprofen 400mg',                'Analgesics',         90, 30,  '2026-10-31', 1),
(6,  'ORS Sachets',                    'Rehydration',        60, 100, '2027-05-31', 4),
(7,  'Ceftriaxone 1g Injection',       'Antibiotics',        25, 20,  '2026-09-30', 2),
(8,  'Amlodipine 5mg',                 'Cardiovascular',    140, 40,  '2028-06-30', 3),
(9,  'Salbutamol Inhaler 100mcg',      'Respiratory',        12, 15,  '2026-11-30', 1),
(10, 'Omeprazole 20mg',                'Gastrointestinal',   55, 25,  '2027-09-30', 4),
(11, 'Cetirizine 10mg',                'Antihistamines',     35, 20,  '2026-11-25', 1),
(12, 'Vitamin C 500mg',                'Vitamins',          200, 50,  '2027-12-31', 3),
(13, 'Diclofenac Gel 1%',              'Topical',             0, 10,  '2027-04-30', 4),
(14, 'Mebendazole 100mg',              'Antiparasitics',     45, 20,  '2026-07-31', 2);

-- ------------------------------------------------------------
-- Alerts (active low/expired + one resolved history)
-- ------------------------------------------------------------
INSERT INTO stock_alerts (medicine_id, alert_type, triggered_at, resolved, resolved_at) VALUES
(2,  'low',      NOW() - INTERVAL 2 DAY,  0, NULL),
(4,  'low',      NOW() - INTERVAL 1 DAY,  0, NULL),
(6,  'low',      NOW() - INTERVAL 3 DAY,  0, NULL),
(9,  'low',      NOW() - INTERVAL 6 HOUR, 0, NULL),
(13, 'low',      NOW() - INTERVAL 2 HOUR, 0, NULL),
(5,  'expired',  NOW() - INTERVAL 12 DAY, 0, NULL),
(7,  'expired',  NOW() - INTERVAL 43 DAY, 0, NULL),
(14, 'expired',  NOW() - INTERVAL 104 DAY, 0, NULL),
(1,  'low',      NOW() - INTERVAL 21 DAY, 1, NOW() - INTERVAL 18 DAY);
