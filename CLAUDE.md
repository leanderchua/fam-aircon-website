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
| `primary` | `#1E5F75` | Main brand teal — headings, logo text, key UI elements |
| `primary-dark` | `#0f3344` | Dark navy-teal — nav logo wordmark, stats band, footer background |
| `secondary` | `#29B5E8` | Interactive cyan — active nav links, accents, highlights |
| `secondary-light` | `#b8e8f7` | Light cyan — stat suffixes on dark backgrounds |
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

**Status: live on Hostinger.** `index.php` is deployed and DB-driven at `https://famairconditioningsupply.com`. `index.html` still exists in the repo and is still what GitHub Pages serves at `https://leanderchua.github.io/fam-aircon-website/` — it's left untouched as a fallback, not actively maintained.

### Local Testing → Deploy Workflow

**Every change goes through this order — test locally first, deploy only after it passes:**

1. **Make the change** in the repo files.
2. **Test on localhost** before pushing anything:
   ```powershell
   C:\xampp\mysql_start.bat        # start local MySQL (fam_cms DB)
   php -S localhost:8000            # from repo root
   ```
   Verify the change at `http://localhost:8000` (public site) or `http://localhost:8000/admin/...` (admin panel). Stop both processes when done.
3. **Stop and wait for explicit sign-off before committing anything.** Report what was tested and the results, then wait for the user to say it's good — don't decide unilaterally that local testing passed and proceed straight to commit/push/deploy. This is a hard checkpoint, not a formality.
4. **Only after that sign-off**, push both targets together:
   - `git add` the changed files, commit, `git push` (GitHub, `master`)
   - Rebuild the deploy archive (runtime files only — `index.php`, `admin/`, `includes/`, `css/`, `js/`, `images/`, `config/config.php` swapped for the production `config/config.prod.php`, `uploads/`, root `.htaccess`) and redeploy to Hostinger via the same zip-upload process used for the initial cutover.
   - Verify the live site after redeploy.

`config/config.php` in the working tree always holds **local dev** credentials (127.0.0.1 / `fam_cms` / root). `config/config.prod.php` (also gitignored, never committed) holds the real Hostinger DB credentials — it only gets swapped in when building the deploy archive, never copied over the local dev config.

**Uploaded files survive redeploys — documented assumption, not a silent one.** The deploy archive's `uploads/` only ever contains `.htaccess`/`.gitkeep` (real uploaded images are never in git or in the archive); Hostinger's static-archive extraction adds/overwrites files but does not purge pre-existing ones outside the archive (empirically verified — an unrelated file, `inventory-config.php`, survived a redeploy untouched). So production's live `uploads/` folder accumulates real admin-uploaded content over time with **no corresponding git history** — redeploying code is not a clean-slate operation for that folder, by design.

**Local environment note**: `php -S localhost:8000` actually runs a WinGet-installed PHP 8.3 (`...WinGet\Packages\PHP.PHP.8.3_Microsoft.Winget.Source_...\php.ini`), **not** XAMPP's PHP — XAMPP is only used for MySQL (`mysql_start.bat`). That `php.ini` needed `extension=fileinfo` and `extension=gd` uncommented, and `upload_max_filesize` raised from `2M` to `8M`, to make the image-upload feature testable locally (`finfo_open()`/GD functions are undefined without those extensions). Restart `php -S` after any `php.ini` edit — it's read once at process start.

### Hostinger Account Layout

Same Hostinger account (`u311097277`) hosts three separate things under `famairconditioningsupply.com` — be careful not to let a deploy touch the other two:
- **Main domain** (`famairconditioningsupply.com`) — this site's `index.php` + CMS, document root also contains `inventory-config.php` and root `.htaccess` (which must keep the rule blocking direct access to that file — see the merged `.htaccess` built during deploy).
- **`inventory.famairconditioningsupply.com`** — a separate Node/PHP inventory app, path-based subdomain sharing the main domain's document root under `/inventory`, own DB (`u311097277_Inventory`). Not related to this project — never overwrite.
- **`services.famairconditioningsupply.com`** — separate addon domain, own document root. Not related to this project.

Production DB: `u311097277_fam_cms` (phpMyAdmin access via Hostinger's `hosting_getPhpMyAdminLinkV1`, no local MySQL client available for direct import). Admin login: `admin` at `/admin/login.php` — password was rotated after initial deploy; use "Change Password" in `/admin/admins.php` if it needs resetting, not a plaintext lookup anywhere.

Local dev environment: XAMPP installed at `C:\xampp` (MySQL running via `mysql_start.bat`). PHP app server run locally with `php -S localhost:8000` from the repo root (no Apache vhost yet — `.htaccess` upload-lockdown rules aren't exercised by the built-in server, so those get verified later under real Apache/Hostinger). DB: `fam_cms` on `127.0.0.1`, schema in `db/schema.sql`, seed data in `db/seed.sql` (transcribed verbatim from the original `index.html`). Local admin login: `admin` / `fam12345` (test-only, must be changed before Hostinger deploy) at `http://localhost:8000/admin/login.php`.

Admin CRUD screens live at `/admin/{stats,about_checklist,services,brands,contact_info,nav_links}.php`, all built on the shared generic renderer `admin/includes/crud.php` (list + reorder + add/edit/delete, CSRF-protected, prepared statements, `htmlspecialchars()` on all output, live image-preview inputs) driven by a per-file field-spec config. `/admin/settings.php` edits the `site_settings` singleton directly (different pattern, no add/delete/reorder). `/admin/projects.php` is a **standalone hand-built screen** (like `/admin/admins.php`), not on `crud.php`, because it manages a one-to-many relationship — one Card Image plus any number of Gallery Images per project — that the generic single-flat-row renderer can't express. Image fields keep the original paste-a-path/URL text input (still needed — hero/about backgrounds and most project photos reference external Unsplash URLs, brand logos reference git-tracked SVGs in `images/brands/`) alongside a real file-upload control; uploading takes precedence over the pasted value for that submit. Upload handling lives in `includes/uploads.php`: `fam_handle_image_upload()` (single file, used by `crud.php`/`settings.php`/the Card Image field), `fam_handle_multiple_image_uploads()` (array-shaped `$_FILES`, used by the Gallery Images field — one bad file in a batch never blocks the others, just gets listed in a non-blocking warning), and `fam_cleanup_old_upload()` (shared by all of the above).

`index.php` (new): fully renders from the DB via `includes/functions.php`'s `fetchX()` helpers — nav, hero, stats, about, services, projects, brands, contact, footer all DB-driven; verified visually/structurally equivalent to `index.html`. The public contact form now really works (CSRF-protected, inserts into `contact_submissions`, best-effort `mail()` to `site_settings.contact_recipient_email`) instead of the old fake client-side-only submit. Project galleries are DB-driven via a `project_photos` child table (`project_id`, `photo_path`, `photo_alt`, `sort_order`, `ON DELETE CASCADE`) and `fetchProjectPhotos()` — a project is only clickable on the public site (cursor, hover icon, `role="button"`) if it actually has ≥1 gallery photo, an honest signal instead of the old always-on decoration. Each clickable card carries the images (Card Image first, then Gallery Images) as server-rendered JSON in a `data-gallery` attribute, read directly by `js/main.js` — no more slug-matching between PHP and JS. **Production migration not yet run**: `db/migrate_project_photos.sql` must be applied once via phpMyAdmin against the live `u311097277_fam_cms` database before this is fully live there (adds the `project_photos` table and backfills the 6 existing projects' 18 gallery photos that used to be hardcoded in `js/main.js`) — until then, production's projects show 0 gallery photos and are temporarily non-clickable, which is expected mid-rollout. Remaining known limitation, unchanged: the projects bento-grid tile spans and the brands PNG-vs-SVG logo sizing are positional/heuristic-based in `index.php`, not DB-driven.

Progress:
- [x] Architecture, DB schema, security checklist, migration plan designed (see plan file)
- [x] PHP+MySQL environment available (local XAMPP)
- [x] DB schema created (`db/schema.sql`, local `fam_cms` database)
- [x] Admin login + auth built (`includes/auth.php`, `admin/login.php` — session, CSRF, lockout all verified locally)
- [x] Content seeded from current index.html (`db/seed.sql` — verified row counts match)
- [x] Admin CRUD screens built (all 7 repeatable content types + settings singleton, verified add/edit/reorder/delete/CSRF-rejection against the real local DB)
- [x] Admin UI/UX reworked (icon sidebar + mobile drawer, live image previews, accessible forms/tables)
- [x] index.html converted to index.php, DB-driven, verified locally (index.html untouched, still the live GitHub Pages file)
- [x] Deploy target cut over from GitHub Pages to Hostinger — live at `https://famairconditioningsupply.com`
- [x] Admin account management screen (`/admin/admins.php` — add admin, change password, delete with last-admin/self-delete guards)
- [x] Real file upload for image fields (`includes/uploads.php` — `finfo_file()` MIME sniff, GD re-encode, random filenames)
- [x] Multi-photo project galleries, DB-driven (`project_photos` table, standalone `/admin/projects.php`, honest clickability) — verified locally; production migration (`db/migrate_project_photos.sql`) not yet run

**Why**: content (services, projects, brand logos, stats, contact info) is currently hardcoded in `index.html` and requires a code change + `git push` to edit. The owner wants to edit this content and upload pictures themselves via a simple admin panel, without touching code. Hosting is moving to **Hostinger Business** (PHP + MySQL) to make this possible — GitHub Pages cannot run a backend.

**Approach**: `index.html` → `index.php`, MySQL as the single source of truth, rendered server-side. The Tailwind CDN, design tokens, fonts, icons, and "no build step" rule are all **unaffected** — PHP is a server-side templating layer, not a frontend bundler. Every existing Tailwind class and section pattern gets copied verbatim into PHP partials that loop over DB-fetched arrays instead of the current copy-pasted HTML blocks.

**Scope**: everything repeatable becomes DB-driven and admin-editable — Stats, Services, Brands, Projects, About checklist, Contact info blocks, Nav links, plus a singleton Settings row (hero/about copy, footer blurb, contact recipient email). Single admin login, no multi-user roles, no versioning/drafts/scheduling.

**New structure**: `admin/` (login + one CRUD screen per content type), `includes/` (`db.php`, `auth.php`, `functions.php`, `partials/`), `config/` (`config.php` — gitignored — + committed `config.sample.php`), `uploads/` (admin-uploaded images, locked down via `.htaccess` to deny PHP execution).

**Security is mandatory, not optional** (public-facing site, shared hosting): PDO prepared statements only (never string-concatenated SQL); `htmlspecialchars()` on every DB value echoed into HTML; CSRF tokens on all admin forms and the public contact form; `password_hash()`/`password_verify()` + session regeneration + login rate-limiting for auth; `uploads/.htaccess` blocks PHP execution in that folder (the standard shared-hosting shell-upload defense); `display_errors=0` in production; DB credentials never committed.

**Upload validation** (`includes/uploads.php`, `fam_handle_image_upload()`): 5 MB cap (`FAM_MAX_UPLOAD_BYTES`); real MIME sniffed via `finfo_file()` on the file bytes (never the client-supplied `$_FILES[...]['type']`), restricted to `image/jpeg`/`image/png`/`image/webp` — SVG is disallowed simply by being absent from that allow-map, not a special case; re-decoded and re-written through GD (`imagecreatefromjpeg`/`png`/`webp` → `imagejpeg`/`imagepng`/`imagewebp`), which both strips any embedded payload and rejects files that sniff as an image MIME but aren't actually decodable; saved under a random 32-hex-char filename (`bin2hex(random_bytes(16))`). `fam_cleanup_old_upload()` only deletes files matching that exact generated shape (`uploads/[a-f0-9]{32}.(jpg|png|webp)`), so it never touches `images/...` assets or pasted external URLs.

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
