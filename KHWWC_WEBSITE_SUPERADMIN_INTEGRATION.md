# KHCWW Website + Super Admin Integration

This document defines the port boundary for the public KHCWW website and the Website Management area. The existing Laravel application remains the source of truth for members, users, roles, authentication, registration, payments, contributions, wallets, documents and mail transport.

## Access model

- Public: published website content only.
- Authenticated member: existing member dashboard and private records.
- Admin: website content management according to existing permissions.
- Super Admin: global website settings, SEO identity, navigation, social, email templates and publishing controls.
- Private member data, financial data, private documents and unpublished drafts must never be returned by public endpoints.

## Public API contracts

| Resource | Method | Endpoint | Status |
|---|---|---|---|
| Homepage/settings | GET | `/api/public/website/homepage` | NEW LARAVEL CONTRACT REQUIRED |
| Pages | GET | `/api/public/website/pages/{slug}` | NEW LARAVEL CONTRACT REQUIRED |
| Projects | GET | `/api/public/website/projects/{slug?}` | NEW LARAVEL CONTRACT REQUIRED |
| Articles | GET | `/api/public/website/articles/{slug?}` | NEW LARAVEL CONTRACT REQUIRED |
| Events | GET | `/api/public/website/events/{slug?}` | NEW LARAVEL CONTRACT REQUIRED |
| Welfare programmes | GET | `/api/public/website/welfare` | NEW LARAVEL CONTRACT REQUIRED |
| Public profiles | GET | `/api/public/website/people/{slug?}` | NEW LARAVEL CONTRACT REQUIRED |
| Gallery/albums | GET | `/api/public/website/gallery/{slug?}` | NEW LARAVEL CONTRACT REQUIRED |
| Published resources | GET | `/api/public/website/resources` | NEW LARAVEL CONTRACT REQUIRED |
| Search | GET | `/api/public/website/search?q={query}` | NEW LARAVEL CONTRACT REQUIRED |
| Contact enquiry | POST | `/api/public/contact-enquiries` | NEW LARAVEL CONTRACT REQUIRED |
| Registration handoff | GET/POST | Existing registration routes | EXISTING LARAVEL CONTRACT |
| Member login | POST | Existing authentication route | EXISTING LARAVEL CONTRACT |

Published responses should include `id`, `slug`, `title`, `excerpt`, `content`, `published_at`, `updated_at`, `status`, `featured_image`, `seo`, `canonical_url`, `sitemap_included`, and `structured_data_type` where applicable.

## People and profiles

The public profile record must reference an existing Laravel member/user through `member_id`. It may contain `title_prefix`, `display_name`, `public_role`, `short_bio`, `biography`, `qualifications`, `department`, `public_email`, `public_phone`, `social_links`, `image_source`, `profile_image_id`, `sort_order`, `published`, and `public_visibility`.

`image_source` must be either `existing_profile_image` or `public_media`. Existing private profile images must not be exposed. Professional prefixes such as Dr., Mr., Ms. or Prof. are explicitly assigned by an authorized administrator; they must never be inferred.

Public profile pages should emit `ProfilePage`, `Person`, and `BreadcrumbList` JSON-LD only when the profile is published and publicly visible.

## Super Admin website management

| Area | Methods | Endpoint | Status |
|---|---|---|---|
| Website dashboard | GET | `/api/admin/website/dashboard` | NEW LARAVEL CONTRACT REQUIRED |
| Homepage sections | GET, PUT | `/api/admin/website/homepage` | NEW LARAVEL CONTRACT REQUIRED |
| Pages/projects/articles/events | GET, POST, PUT, archive | `/api/admin/website/{resource}` | NEW LARAVEL CONTRACT REQUIRED |
| People/profiles | GET, POST, PUT, archive | `/api/admin/website/people` | NEW LARAVEL CONTRACT REQUIRED |
| Media library | GET, POST, PUT, archive | `/api/admin/website/media` | NEW LARAVEL CONTRACT REQUIRED |
| Gallery/albums | GET, POST, PUT, archive | `/api/admin/website/gallery` | NEW LARAVEL CONTRACT REQUIRED |
| Documents | GET, POST, PUT, archive | `/api/admin/website/documents` | NEW LARAVEL CONTRACT REQUIRED |
| Navigation/footer | GET, PUT | `/api/admin/website/navigation` and `/footer` | NEW LARAVEL CONTRACT REQUIRED |
| Contact messages | GET, PATCH | `/api/admin/website/contact-messages` | NEW LARAVEL CONTRACT REQUIRED |
| Website settings | GET, PUT | `/api/admin/website/settings` | NEW LARAVEL CONTRACT REQUIRED |

All write operations require existing Laravel admin authorization, server-side validation, audit logging and safe generic errors. Draft and scheduled content must not appear on public endpoints.

## SEO and Google/Search

| Resource | Method | Endpoint | Status |
|---|---|---|---|
| Organization identity | GET, PUT | `/api/admin/website/seo/organization` | NEW LARAVEL CONTRACT REQUIRED |
| Page SEO | GET, PUT | `/api/admin/website/seo/{resource}/{id}` | NEW LARAVEL CONTRACT REQUIRED |
| SEO health | GET | `/api/admin/website/seo/health` | NEW LARAVEL CONTRACT REQUIRED |
| Sitemap | GET | `/sitemap.xml` | NEW LARAVEL CONTRACT REQUIRED |
| Robots | GET | `/robots.txt` | NEW LARAVEL CONTRACT REQUIRED |
| Search Console settings | GET, PUT | `/api/admin/website/seo/search-console` | NEW LARAVEL CONTRACT REQUIRED |
| Structured data preview | GET | `/api/admin/website/seo/structured-data` | NEW LARAVEL CONTRACT REQUIRED |

SEO fields: `seo_title`, `meta_description`, `canonical_url`, `og_title`, `og_description`, `og_image_id`, `twitter_card`, `robots`, `focus_keyword`, `sitemap_included`, and `structured_data_type`. Calculate health from actual fields; never store a hardcoded score.

Organization JSON-LD should use only verified values such as organization name, alternate name, description, logo, official URL, telephone, email, postal address and configured `sameAs` social URLs. Do not invent identifiers or claim Search Console verification.

The sitemap must contain only published public pages, projects, articles, events, public profiles and resources. It must exclude admin, authentication, dashboards, private documents, financial pages and unpublished content. Robots must disallow those private routes.

## Media and image SEO

Public media fields: `url`, `alt_text`, `caption`, `title`, `description`, `credit`, `copyright`, `mime_type`, `width`, `height`, `visibility`, and `usage`. The server should reject publication of non-decorative public images without alt text. Media storage must use the existing Laravel storage system.

## Email

Use the existing Laravel mail configuration and transport. Website templates may expose safe subject/body fields and an allowlisted variable set such as `{{member_name}}`, `{{registration_reference}}`, `{{amount}}`, and `{{organization_name}}`. Do not permit arbitrary server-side code in templates.

## Demo adapter shape

Recommended client adapter methods are `getHomepage`, `getProjects`, `getArticles`, `getEvents`, `getPublicProfiles`, `getGallery`, `getResources`, `searchWebsite`, `getWebsiteSettings`, `getSeoHealth`, `sendContactEnquiry`, `getRegistrationHandoff`, and `getWebsiteDashboard`. Keep transport and authentication inside the adapter so the UI can be ported to Laravel Blade/components without rebuilding the public experience.
