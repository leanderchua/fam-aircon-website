# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Git & GitHub — Commit After Every Change

**Always commit and push after completing any piece of work.** Never leave work uncommitted.

```powershell
git add index.html css/styles.css js/main.js   # stage only changed files
git commit -m "Short imperative summary of what changed"
git push
```

Commit message format: imperative mood, present tense, under 72 characters.  
Remote: `https://github.com/leanderchua/fam-aircon-website` (public)  
GitHub Pages: `https://leanderchua.github.io/fam-aircon-website/`

---

## Running the App

**Static site — open directly in browser:**
```powershell
Start-Process "index.html"
```

No build step, no server required. All assets are local or CDN-loaded.

---

## Architecture

Pure static site — no framework, no bundler, no Node.js.

| File | Purpose |
|------|---------|
| `index.html` | All page sections (Hero, Stats, About, Services, Projects, Contact), nav, mobile menu, all markup |
| `css/styles.css` | Custom CSS — overrides, animations, component styles not covered by Tailwind |
| `js/main.js` | All JavaScript — nav scroll behaviour, mobile menu toggle, stat counters, any interactive features |
| `images/` | Logo and all local image assets |

### Tailwind CSS

Loaded via CDN (`https://cdn.tailwindcss.com?plugins=forms,container-queries`). Config is defined inline in `index.html` as `tailwind.config = { ... }`.

**Never import Tailwind from npm or add a build step** — always use the CDN version.

### Design Tokens (defined in `tailwind.config` inside `index.html`)

| Token | Value | Usage |
|-------|-------|-------|
| `primary` | `#00164e` | Main brand navy — headings, logo text, key UI elements |
| `secondary` | `#0051d5` | Interactive blue — active nav links, accents, highlights |
| `secondary-light` | `#b4c5ff` | Light blue — stat suffixes on dark backgrounds |
| `cta` | `#f97316` | Orange — CTA buttons ("Get a Quote", "Schedule Consultation") |
| `cta-hover` | `#ea580c` | Orange hover state |
| `surface` | `#f7f9fb` | Page background |
| `surface-dim` | `#e6e8ea` | Hover backgrounds, subtle fills |
| `surface-bright` | `#ffffff` | Card/panel backgrounds |
| `on-surface` | `#191c1e` | Primary body text |
| `on-surface-variant` | `#45464d` | Secondary body text |
| `outline-variant` | `#c6c6cd` | Subtle borders, dividers |
| `outline` | `#76777d` | Default borders |

**Never hardcode hex values.** Always use the token names above (e.g. `bg-primary`, `text-secondary`, `border-outline-variant`).

### Typography

| Font | Variable | Usage |
|------|----------|-------|
| Inter | `font-display`, `font-body` | All headings and body copy |
| JetBrains Mono | `font-label` | Labels, tags, button text, tracking-widest uppercase strings |

### Icons

Material Symbols Outlined via Google Fonts CDN. Usage:
```html
<span class="material-symbols-outlined">icon_name</span>
<!-- Filled variant: -->
<span class="material-symbols-outlined fill-icon">icon_name</span>
```

**Never use emojis as icons.**

### Layout

- Max content width: `max-w-container` (1280px), centered with `mx-auto`
- Horizontal padding: `px-4 md:px-12`
- Fixed nav height: `h-20` → main content starts at `pt-20`

---

## Design Consistency Rules

**When creating new pages or sections, do not deviate from the current design.**

- Maintain the same color tokens, fonts, and spacing patterns as existing sections
- New pages must use the same fixed nav (`<header id="nav">`) and match the existing visual language
- Sections follow the pattern: full-width background → `max-w-container mx-auto px-4 md:px-12` inner wrapper
- CTA buttons always use `bg-cta hover:bg-cta-hover` with `font-label text-xs uppercase tracking-[0.15em]`
- Section headings follow: eyebrow label (`font-label text-xs text-secondary uppercase tracking-[0.15em]`) → `<h2>` in `font-display font-extrabold text-primary`
- Alternating section backgrounds: `bg-white` ↔ `bg-surface`
- All borders use `border-outline-variant`

---

## UI/UX Skill — Use for Every Visual Change

**Invoke the `ui-ux-pro-max` skill before implementing any UI change, new section, or new page.**

Skill location: `c:\Users\iRockFTW\Desktop\Claude\Fam Service Management\.claude\skills\ui-ux-pro-max`

This project's design profile for the skill:
- **Product type**: Service business website (HVAC / Air Conditioning)
- **Style**: Professional, clean, industrial, minimal
- **Industry**: Construction / Engineering / Home Services
- **Stack**: `html-tailwind` (default)

Run the design system query before any significant UI work:
```bash
python3 "c:\Users\iRockFTW\Desktop\Claude\Fam Service Management\.claude\skills\ui-ux-pro-max\scripts\search.py" "hvac aircon service professional industrial minimal" --design-system -p "FAM Airconditioning Supply"
```

---

## Claude Communication Style

- **No preamble** — don't say "Sure!", "Great question", or restate what you're about to do. Just do it.
- **No trailing summaries** — don't recap what you just did at the end of a response.
- **No bullet-point explanations of obvious steps** — skip narrating each file edit.
- **Inline updates only** — one short sentence when direction changes or something is found; silent otherwise.
- **Code > prose** — show the change, don't describe it.
- **No filler phrases** — "Let me", "I'll now", "As you can see", "Note that", etc. are banned.
- **End of turn**: one sentence max — what changed and what's next, nothing else.

---

## Planned: Custom CMS (PHP + MySQL on Hostinger Business)

**Status: blocked — waiting on a PHP+MySQL hosting environment (Hostinger Business or a local stack like XAMPP/Laragon).** Until that's in place, all site changes stay static-only (`index.html` / `css/styles.css` / `js/main.js`), per the existing workflow above. Do not start CMS implementation until explicitly told to — the user will say when to start.

Progress:
- [x] Architecture, DB schema, security checklist, migration plan designed (see plan file)
- [ ] PHP+MySQL environment available (Hostinger Business or local XAMPP/Laragon)
- [ ] DB schema created
- [ ] Admin login + auth built
- [ ] Content seeded from current index.html
- [ ] Admin CRUD screens built
- [ ] index.html sections converted to index.php one by one
- [ ] Deploy target cut over from GitHub Pages to Hostinger

**Why**: content (services, projects, brand logos, stats, contact info) is currently hardcoded in `index.html` and requires a code change + `git push` to edit. The owner wants to edit this content and upload pictures themselves via a simple admin panel, without touching code. Hosting is moving to **Hostinger Business** (PHP + MySQL) to make this possible — GitHub Pages cannot run a backend.

**Approach**: `index.html` → `index.php`, MySQL as the single source of truth, rendered server-side. The Tailwind CDN, design tokens, fonts, icons, and "no build step" rule are all **unaffected** — PHP is a server-side templating layer, not a frontend bundler. Every existing Tailwind class and section pattern gets copied verbatim into PHP partials that loop over DB-fetched arrays instead of the current copy-pasted HTML blocks.

**Scope**: everything repeatable becomes DB-driven and admin-editable — Stats, Services, Brands, Projects, About checklist, Contact info blocks, Nav links, plus a singleton Settings row (hero/about copy, footer blurb, contact recipient email). Single admin login, no multi-user roles, no versioning/drafts/scheduling.

**New structure**: `admin/` (login + one CRUD screen per content type), `includes/` (`db.php`, `auth.php`, `functions.php`, `partials/`), `config/` (`config.php` — gitignored — + committed `config.sample.php`), `uploads/` (admin-uploaded images, locked down via `.htaccess` to deny PHP execution).

**Security is mandatory, not optional** (public-facing site, shared hosting): PDO prepared statements only (never string-concatenated SQL); `htmlspecialchars()` on every DB value echoed into HTML; CSRF tokens on all admin forms and the public contact form; `password_hash()`/`password_verify()` + session regeneration + login rate-limiting for auth; uploads validated by real file bytes (`finfo_file()`), re-encoded through GD, given random filenames, and SVG uploads disallowed entirely (brand logos go PNG/WebP); `uploads/.htaccess` blocks PHP execution in that folder (the standard shared-hosting shell-upload defense); `display_errors=0` in production; DB credentials never committed.

**Migration is incremental**: seed the DB from current hardcoded content first (day one looks pixel-identical), build admin CRUD, then convert `index.html` → `index.php` one section at a time with visual-parity checks against the live site after each, before cutting over the deploy target from GitHub Pages to Hostinger.

**Full plan with exact DB schema (table/column definitions), file structure, and step-by-step migration order**: `C:\Users\Leander\.claude\plans\plan-me-a-custom-lazy-sundae.md`

---

## Key Conventions

- **All pages/sections are in `index.html`** — this is a single-file site. Do not create separate HTML pages unless explicitly asked.
- **`css/styles.css`** — custom styles only. Prefer Tailwind utilities for everything else.
- **`js/main.js`** — all interactivity lives here. No inline `<script>` blocks in HTML except the Tailwind config.
- **Responsive breakpoint**: `md` (768px) is the primary mobile→desktop breakpoint.
- **Mobile menu**: toggled via `#navToggle` button, targets `#mobileMenu` with `hidden` class.
- **Stat counters**: `.stat-val` elements with `data-target` attributes, animated on scroll via `main.js`.
- **Smooth scroll**: `<html class="scroll-smooth">` — use anchor `href="#section-id"` for nav links.
