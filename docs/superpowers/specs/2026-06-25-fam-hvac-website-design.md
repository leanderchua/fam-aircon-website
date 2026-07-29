# FAM Air Conditioning Supply — Website Design Spec

## Context

FAM Air Conditioning Supply needs a company profile website to establish their online presence. They serve both commercial and residential clients in the Philippines, offering full-service HVAC solutions (supply, installation, maintenance, repair, consultation). The website should be clean, minimalist, and professional — reflecting their tagline **"Cool Air. Cool Life."**

## Approach

**Single-page scrolling website** built with plain HTML, CSS, and vanilla JavaScript. No frameworks, no build tools. Easy to host on any platform (GitHub Pages, Netlify, shared hosting).

## Brand Identity

| Element | Value |
|---------|-------|
| Company Name | FAM Air Conditioning Supply |
| Tagline | Cool Air. Cool Life. |
| Primary Color (Dark Teal) | `#1C4E5C` |
| Accent Color (Sky Blue) | `#29B6F6` |
| White | `#FFFFFF` |
| Light Gray (Alt BG) | `#F5F7FA` |
| Body Text | `#2D3436` |
| Typography | Inter (Google Fonts), with system sans-serif fallback |
| Logo | FAM HVAC handshake "M" logo (provided as PNG) |

## File Structure

```
FAM - Website/
├── index.html          # Single-page site
├── css/
│   └── styles.css      # All styles
├── js/
│   └── main.js         # Navigation, scroll, interactions
├── images/
│   ├── logo.png        # FAM HVAC logo
│   ├── hero-bg.jpg     # Hero background (placeholder)
│   ├── about.jpg       # About section image (placeholder)
│   └── portfolio/      # Portfolio images (placeholders)
│       ├── project-1.jpg
│       ├── project-2.jpg
│       └── ...
└── docs/               # Design docs (this file)
```

## Page Sections

### 1. Navbar (Fixed Top)

- Logo on the left
- Navigation links on the right: Home, About, Services, Portfolio, Contact
- Smooth scroll to corresponding sections on click
- Transparent on hero → solid dark teal background on scroll
- Mobile: hamburger menu icon, full-width dropdown nav overlay

### 2. Hero Section

- Full viewport height (`100vh`)
- Background: placeholder HVAC image with dark teal gradient overlay (`rgba(28,78,92,0.7)`)
- Content centered:
  - FAM logo (optional, since navbar has it)
  - **H1**: "Cool Air. Cool Life."
  - **Subtitle**: "Your trusted partner in air conditioning supply, installation, and servicing"
  - Two CTA buttons:
    - "Get a Quote" — solid sky blue (`#29B6F6`), scrolls to Contact
    - "Our Services" — outlined white, scrolls to Services
- Animated scroll-down chevron at bottom

### 3. About Us

- **Background**: White
- **Layout**: Two columns (60/40) on desktop, stacked on mobile
- **Left**: 
  - Section heading: "About FAM Air Conditioning Supply"
  - 2-3 paragraphs placeholder company profile text
- **Right**:
  - Placeholder image (team photo, office, or installation)
- **Below — Stats Row**: 
  - Horizontal strip with 4 counters (placeholder values):
    - "500+" Projects Completed
    - "10+" Years Experience  
    - "50+" Commercial Clients
    - "24/7" Service Support
  - Numbers in sky blue, labels in dark teal
- **Below — Brand Partners**:
  - Heading: "Trusted Brands We Work With"
  - Horizontal row of grayscale logos: Carrier, Mitsubishi Electric, Daikin
  - Logos turn to color on hover

### 4. Services

- **Background**: Light gray (`#F5F7FA`)
- **Heading**: "Our Services"
- **Layout**: 6 cards in responsive grid (3-col desktop, 2-col tablet, 1-col mobile)
- Each card:
  - White background, subtle box-shadow
  - Line icon in sky blue (using a free icon set like Lucide or Font Awesome)
  - Service title in dark teal (bold)
  - 1-2 sentence description
  - Hover: slight Y-translate lift + sky blue top-border accent

**Service Cards:**

| # | Icon | Title | Description |
|---|------|-------|-------------|
| 1 | Snowflake | AC Supply | Wide range of split-type, cassette, and VRF units from top brands |
| 2 | Wrench | Installation | Professional installation for residential and commercial spaces |
| 3 | Shield | Preventive Maintenance | Regular cleaning, check-ups, and PMS schedules to keep your systems running efficiently |
| 4 | Tool | Repair & Troubleshooting | Fast diagnosis and repair for all AC brands and unit types |
| 5 | Blueprint | System Design & Consultation | Site surveys, load calculations, and system recommendations for new builds and renovations |
| 6 | Headset | After-Sales Support | Dedicated support and warranty service for all installed units |

### 5. Portfolio

- **Background**: White
- **Heading**: "Our Projects"
- **Filter tabs**: All | Residential | Commercial | Maintenance
  - Tab styling: pill-shaped, active tab in sky blue
  - JavaScript filters which cards are visible
- **Grid**: 6 items (3-col desktop, 2-col tablet, 1-col mobile)
- Each item:
  - Placeholder project photo, consistent 4:3 aspect ratio
  - CSS hover overlay (dark teal semi-transparent) showing:
    - Project name (e.g., "One Wilson Square — VRF System Installation")
    - Category tag
  - Subtle zoom effect on hover

### 6. Contact

- **Background**: Light gray (`#F5F7FA`)
- **Heading**: "Get in Touch"
- **Layout**: Two columns (55/45) on desktop, stacked on mobile
- **Left — Form**:
  - Fields: Name (text), Email (email), Phone (tel), Service Needed (dropdown with the 6 services), Message (textarea)
  - Submit button: "Send Message" in sky blue
  - Form is front-end only — action can be connected to Formspree, EmailJS, or a backend later
- **Right — Contact Info**:
  - Phone: placeholder
  - Email: placeholder
  - Address: placeholder
  - Business Hours: placeholder (e.g., Mon-Sat 8AM-5PM)
  - Embedded Google Map iframe (placeholder coordinates) or static map image

### 7. Footer

- **Background**: Dark teal (`#1C4E5C`)
- **Text**: White
- Three columns on desktop:
  - Left: Logo + short tagline
  - Center: Quick nav links (Home, About, Services, Portfolio, Contact)
  - Right: Social media icons (Facebook, Instagram — placeholders)
- Bottom bar: copyright line "2026 FAM Air Conditioning Supply. All Rights Reserved."

## Interactions & JavaScript

- **Smooth scroll**: Navbar links scroll smoothly to sections
- **Navbar transition**: Transparent → solid background after scrolling past hero
- **Mobile menu**: Hamburger toggle for mobile nav
- **Portfolio filter**: Tab-based filtering of portfolio cards (show/hide with CSS transitions)
- **Scroll animations**: Subtle fade-in on scroll for section elements (using Intersection Observer)
- **Stats counter**: Animated number count-up when the About section comes into view

## Responsive Breakpoints

| Breakpoint | Behavior |
|------------|----------|
| > 1024px | Full desktop layout — 3-col grids, 2-col sections |
| 768-1024px | Tablet — 2-col grids, hamburger menu |
| < 768px | Mobile — 1-col everything, stacked layout, hamburger menu |

## Assets Needed (Placeholders)

All images will use placeholder boxes with descriptive text. The user will replace them with real photos later:

- Hero background (HVAC installation wide shot)
- About section image (team/office)
- 6-9 portfolio project images
- Brand logos: Carrier, Mitsubishi Electric, Daikin (can use text placeholders or SVGs)

## Out of Scope

- Backend / server-side logic
- CMS integration
- Blog section
- E-commerce / online ordering
- Multi-language support
- User authentication

## Verification Plan

1. Open `index.html` directly in browser to verify all sections render
2. Test responsive layout at 1440px, 1024px, 768px, and 375px widths
3. Verify smooth scroll navigation works for all nav links
4. Test mobile hamburger menu open/close
5. Test portfolio filter tabs toggle correctly
6. Verify navbar background changes on scroll
7. Check all hover effects on service cards and portfolio items
8. Validate HTML with W3C validator
9. Test in Chrome, Firefox, and Edge
