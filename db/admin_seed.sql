-- Production admin user. Import after schema.prod.sql and seed.prod.sql,
-- into the u311097277_fam_cms database. Login: admin / (see deploy notes —
-- change this password after first login).

INSERT INTO admin_users (username, password_hash) VALUES
('admin', '$2y$10$5vqHbyxbGdL03EiQ5FNIIO/lqGqJiqfRB.zMV76rRLdFEbQKJh/bG');
