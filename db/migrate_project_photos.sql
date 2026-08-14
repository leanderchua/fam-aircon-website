-- Adds project_photos (the multi-photo project gallery) to an existing
-- production database and backfills the 6 currently-seeded projects with the
-- 3 gallery photos each that used to be hardcoded in js/main.js.
--
-- Run ONCE via phpMyAdmin against the live u311097277_fam_cms database.
-- The INSERTs below have no idempotency guard — running this twice
-- duplicates all 18 rows. The CREATE TABLE is IF NOT EXISTS only so the
-- script is safe to re-run if it's interrupted partway through.

CREATE TABLE IF NOT EXISTS project_photos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  project_id INT UNSIGNED NOT NULL,
  photo_path VARCHAR(255) NOT NULL, photo_alt VARCHAR(200) NOT NULL, sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
  INDEX (project_id, sort_order)
) ENGINE=InnoDB;

INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1614447413576-b346c641c128?w=1200&q=80', 'Commercial HVAC installation at One Wilson Square', 0 FROM projects WHERE title = 'One Wilson Square';
INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1681042803902-f79c240d8f03?w=1200&q=80', 'Rooftop VRF outdoor units at One Wilson Square', 1 FROM projects WHERE title = 'One Wilson Square';
INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1642749776312-aa42ce20c9f5?w=1200&q=80', 'Installation crew on the rooftop at One Wilson Square', 2 FROM projects WHERE title = 'One Wilson Square';

INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1667983453881-4992fe86ab1b?w=1200&q=80', 'HVAC system at FEU-NRMF Medical Center', 0 FROM projects WHERE title = 'FEU-NRMF Medical Center';
INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1558358235-a0a93f68a52c?w=1200&q=80', 'Air vent detail at FEU-NRMF Medical Center', 1 FROM projects WHERE title = 'FEU-NRMF Medical Center';
INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1621905251918-48416bd8575a?w=1200&q=80', 'Technician inspecting equipment at FEU-NRMF Medical Center', 2 FROM projects WHERE title = 'FEU-NRMF Medical Center';

INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1698479603408-1a66a6d9e80f?w=1200&q=80', 'AC installation at St. Joseph Building', 0 FROM projects WHERE title = 'St. Joseph Building';
INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1615309662243-70f6df917b59?w=1200&q=80', 'Ducting pipework at St. Joseph Building', 1 FROM projects WHERE title = 'St. Joseph Building';
INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1634114582073-c34f96202b65?w=1200&q=80', 'VRF ductwork detail at St. Joseph Building', 2 FROM projects WHERE title = 'St. Joseph Building';

INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1726614846573-c1ac2e6161d1?w=1200&q=80', 'Split-type AC installed at Riverside Family Home', 0 FROM projects WHERE title = 'Riverside Family Home';
INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=1200&q=80', 'Electrical work during the Riverside Family Home retrofit', 1 FROM projects WHERE title = 'Riverside Family Home';
INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1612836639523-2ed74bc0209e?w=1200&q=80', 'Exterior of the Riverside Family Home', 2 FROM projects WHERE title = 'Riverside Family Home';

INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1583954964358-1bd7215b6f7a?w=1200&q=80', 'Residential split-type AC maintenance', 0 FROM projects WHERE title = 'Private Residence Portfolio';
INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1615774925655-a0e97fc85c14?w=1200&q=80', 'Technician servicing a residential AC unit', 1 FROM projects WHERE title = 'Private Residence Portfolio';
INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1757562593192-e15aa89e7876?w=1200&q=80', 'Technician preparing equipment for a residential install', 2 FROM projects WHERE title = 'Private Residence Portfolio';

INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1718203862467-c33159fdc504?w=1200&q=80', 'Multi-unit AC installation at Hillside Townhomes', 0 FROM projects WHERE title = 'Hillside Townhomes';
INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1634114581640-9a1734fae3e5?w=1200&q=80', 'Multi-unit condenser array at Hillside Townhomes', 1 FROM projects WHERE title = 'Hillside Townhomes';
INSERT INTO project_photos (project_id, photo_path, photo_alt, sort_order)
SELECT id, 'https://images.unsplash.com/photo-1660330589827-da8ab7dd3c02?w=1200&q=80', 'Technician working on-site at Hillside Townhomes', 2 FROM projects WHERE title = 'Hillside Townhomes';
