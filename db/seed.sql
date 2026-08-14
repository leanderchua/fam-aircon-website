-- Seed data transcribed verbatim from the current index.html. Run once against fam_cms.
USE fam_cms;

INSERT INTO site_settings (
  id, company_name, logo_path,
  hero_eyebrow, hero_heading_line1, hero_heading_line2, hero_subtitle, hero_bg_image_path,
  hero_cta1_text, hero_cta1_href, hero_cta2_text, hero_cta2_href,
  about_eyebrow, about_heading, about_paragraph1, about_paragraph2, about_image_path,
  services_eyebrow, services_heading, services_intro,
  brands_eyebrow, brands_heading,
  projects_eyebrow, projects_heading,
  contact_eyebrow, contact_heading, contact_intro, contact_recipient_email,
  footer_blurb, copyright_text,
  social_facebook_url, social_linkedin_url, social_email
) VALUES (
  1, 'FAM Airconditioning Supply', 'images/logo.png',
  'FAM Airconditioning Supply', 'Cool Air.', 'Cool Life.',
  'Built for the spaces you work in and the ones you come home to.',
  'https://images.unsplash.com/photo-1615309662243-70f6df917b59?w=1600&h=900&fit=crop&q=80',
  'Schedule Consultation', '#contact', 'View Projects', '#projects',
  'About Us', 'Engineered for Reliability.',
  'At FAM Airconditioning Supply, we handle every stage of your aircon''s life — design, supply, installation, repair, and maintenance — for all major brands. With over a decade of hands-on experience, our technicians bring home service directly to you.',
  'Whether it''s a new unit, a repair, or a routine check-up, we work on all aircon brands and bring the service to your door, covering Metro Manila and nearby provinces.',
  'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=700&h=500&fit=crop&q=80',
  'Core Capabilities', 'Aircon Services, Start to Finish',
  'Design, supply, installation, repair, and maintenance for all aircon brands — delivered as home service across Metro Manila and nearby provinces.',
  'Trusted Partners', 'Brands We Carry',
  'Portfolio', 'Featured Deployments',
  'Get Started', 'Request a Service Visit',
  'Contact our team to schedule a home service visit, request a free estimate, or ask about your aircon unit.',
  'info@famaircon.com',
  'Design, supply, installation, repair, and maintenance for all aircon brands — home service across Metro Manila and nearby provinces.',
  '© 2026 FAM Airconditioning Supply. All rights reserved.',
  NULL, NULL, 'info@famaircon.com'
);

INSERT INTO stats (value_display, count_target, suffix, label, sort_order) VALUES
('50+', 50, '+', 'Projects Completed', 0),
('5+', 5, '+', 'Years Experience', 1),
('50+', 50, '+', 'Commercial Clients', 2),
('500+', 500, '+', 'Units Serviced', 3);

INSERT INTO about_checklist (icon_name, label, sort_order) VALUES
('check_circle', 'All Brands Serviced', 0),
('check_circle', 'Home Service', 1),
('check_circle', 'Metro Manila & Nearby Provinces', 2),
('check_circle', 'Design to Maintenance', 3);

INSERT INTO services (icon_name, title, description, sort_order) VALUES
('inventory_2', 'Supply', 'We supply split-type, window-type, cassette, and multi-split units from major brands.', 0),
('construction', 'Professional Installation', 'Careful, proper installation by experienced technicians as home service at your location, for any brand or unit type.', 1),
('settings_suggest', 'Preventive Maintenance', 'Regular cleaning and check-ups, done at home, to keep your unit running efficiently and extend its lifespan.', 2),
('build', 'Repair & Troubleshooting', 'Fast, reliable diagnosis and repair for any brand, so your aircon is back to cooling as quickly as possible.', 3),
('architecture', 'Design & Consultation', 'Guidance on the right unit type, size, and placement for your space, so you get the best fit and performance.', 4),
('support_agent', 'After-Sales Support', 'Same-day response to questions or concerns after installation, with home service across Metro Manila and nearby provinces.', 5);

INSERT INTO brands (name, logo_path, sort_order) VALUES
('Daikin', 'images/brands/daikin.svg', 0),
('Panasonic', 'images/brands/panasonic.svg', 1),
('Mitsubishi Electric', 'images/brands/mitsubishi-electric.svg', 2),
('Mitsubishi Heavy Industries', 'images/brands/mitsubishi-heavy.svg', 3),
('Midea', 'images/brands/midea.svg', 4),
('Carrier', 'images/brands/carrier.svg', 5),
('Condura', 'images/brands/condura.png', 6),
('LG', 'images/brands/lg.svg', 7);

INSERT INTO projects (title, subtitle, category, photo_path, photo_alt, sort_order) VALUES
('One Wilson Square', 'Complete VRF System Installation', 'Commercial', 'https://images.unsplash.com/photo-1614447413576-b346c641c128?w=800&h=450&fit=crop&q=80', 'Commercial HVAC installation at One Wilson Square', 0),
('FEU-NRMF Medical Center', 'Multi-floor AC System', 'Commercial', 'https://images.unsplash.com/photo-1667983453881-4992fe86ab1b?w=800&h=450&fit=crop&q=80', 'HVAC system at FEU-NRMF Medical Center', 1),
('St. Joseph Building', 'VRF System & Ducting', 'Commercial', 'https://images.unsplash.com/photo-1698479603408-1a66a6d9e80f?w=800&h=450&fit=crop&q=80', 'AC installation at St. Joseph Building', 2),
('Riverside Family Home', 'Whole-House Split-Type Retrofit', 'Residential', 'https://images.unsplash.com/photo-1726614846573-c1ac2e6161d1?w=800&h=450&fit=crop&q=80', 'Split-type AC installed at Riverside Family Home', 3),
('Private Residence Portfolio', 'Split-Type & Cassette Installations', 'Residential', 'https://images.unsplash.com/photo-1583954964358-1bd7215b6f7a?w=800&h=450&fit=crop&q=80', 'Residential split-type AC maintenance', 4),
('Hillside Townhomes', 'Multi-Unit AC Installation & Maintenance', 'Residential', 'https://images.unsplash.com/photo-1718203862467-c33159fdc504?w=1200&h=450&fit=crop&q=80', 'Multi-unit AC installation at Hillside Townhomes', 5);

-- Gallery photos for the projects section (matched by title, robust to id drift)
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

INSERT INTO contact_info_blocks (icon_name, label, value_text, sort_order) VALUES
('location_on', 'Office Address', '44-B Jaguar Village East\nCainta, Rizal 1900', 0),
('call', 'Phone', '(0917) 000 0000', 1),
('mail', 'Email', 'info@famaircon.com', 2),
('schedule', 'Operating Hours', 'Mon – Sat: 8:00 AM – 5:00 PM\nEmergency Calls: Same-Day Response', 3);

INSERT INTO nav_links (label, href, sort_order) VALUES
('Home', '#home', 0),
('About', '#about', 1),
('Services', '#services', 2),
('Projects', '#projects', 3),
('Brands', '#brands', 4),
('Contact', '#contact', 5);
