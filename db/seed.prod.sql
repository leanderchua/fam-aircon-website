-- Seed data transcribed verbatim from the current index.html (Hostinger production).
-- Import after schema.prod.sql, into the u311097277_fam_cms database.

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
