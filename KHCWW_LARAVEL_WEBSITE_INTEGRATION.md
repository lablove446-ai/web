# KHCWW Laravel Website Integration

This demo separates public presentation from the existing KHCWW application. The in-memory adapter shown in the prototype is the replacement boundary for Laravel services/controllers. No duplicate users, members, payments, wallets, contributions or registration logic should be introduced.

## Contract status

`EXISTING CONTRACT` means the website should consume the existing KHCWW application contract after endpoint names are confirmed. `NEW LARAVEL CONTRACT REQUIRED` means the existing app needs a small public/CMS endpoint or an adapter around existing services.

## Public content

| Resource | Method | Endpoint | Auth | Status |
|---|---|---|---|---|
| Homepage | GET | `/api/public/website/homepage` | Public | NEW LARAVEL CONTRACT REQUIRED |
| Pages | GET | `/api/public/website/pages/{slug}` | Public | NEW LARAVEL CONTRACT REQUIRED |
| Projects | GET | `/api/public/website/projects`, `/api/public/website/projects/{slug}` | Public | NEW LARAVEL CONTRACT REQUIRED |
| News | GET | `/api/public/website/news`, `/api/public/website/news/{slug}` | Public | NEW LARAVEL CONTRACT REQUIRED |
| Events | GET | `/api/public/website/events`, `/api/public/website/events/{slug}` | Public | NEW LARAVEL CONTRACT REQUIRED |
| Welfare programmes | GET | `/api/public/website/welfare` | Public | NEW LARAVEL CONTRACT REQUIRED |
| Leadership | GET | `/api/public/website/leadership` | Public | NEW LARAVEL CONTRACT REQUIRED |
| Gallery | GET | `/api/public/website/gallery`, `/api/public/website/gallery/{slug}` | Public | NEW LARAVEL CONTRACT REQUIRED |
| Resources | GET | `/api/public/website/resources` | Public | NEW LARAVEL CONTRACT REQUIRED |
| Navigation/footer/settings | GET | `/api/public/website/settings` | Public | NEW LARAVEL CONTRACT REQUIRED |

Responses should return published, visible records only. Content entities should include `id`, `slug`, `title`, `excerpt`, `content`, `status`, `published_at`, `featured`, `media`, `seo`, and `links` where relevant. Dates should be ISO 8601 strings.

## Media

| Method | Endpoint | Auth | Status |
|---|---|---|---|
| GET | `/api/public/media/{id}` | Public only for public media | EXISTING CONTRACT / confirm |
| POST | `/api/admin/website/media` | Existing admin permission | NEW LARAVEL CONTRACT REQUIRED |
| PATCH | `/api/admin/website/media/{id}` | Existing admin permission | NEW LARAVEL CONTRACT REQUIRED |
| DELETE | `/api/admin/website/media/{id}` | Existing admin permission | NEW LARAVEL CONTRACT REQUIRED |

Use Laravel Storage/media infrastructure already in the application. Return `url`, `alt_text`, `caption`, `visibility`, `mime_type`, `width`, `height`, and `usage`. Never expose private/member media through public endpoints.

## Contact and registration handoff

| Method | Endpoint | Auth | Status |
|---|---|---|---|
| POST | `/api/public/contact-enquiries` | Public, rate limited | NEW LARAVEL CONTRACT REQUIRED |
| GET/POST | Existing registration route and payment flow | Public / existing auth flow | EXISTING CONTRACT |
| GET | Existing registration status/reference route | Registration owner | EXISTING CONTRACT |
| POST | Existing login route | Public | EXISTING CONTRACT |

The website must hand off registration to the existing form, validation, member creation, registration fee configuration, payment initiation, payment status and receipt/reference flow. It must not calculate or store a second fee.

Contact request: `name`, `email`, `category`, `message`, `website` honeypot. Response: `message`, `reference`. Validate server-side, rate limit, and use the application's existing mail service.

## CMS endpoints

All CMS endpoints must use existing admin authentication and role/permission checks. The website management area is a navigation group inside the existing admin dashboard, not a new auth system.

| Resource | Methods | Endpoint |
|---|---|---|
| Dashboard | GET | `/api/admin/website/dashboard` |
| Homepage | GET, PUT | `/api/admin/website/homepage` |
| Pages | GET, POST, PUT, DELETE/archive | `/api/admin/website/pages` |
| Projects | GET, POST, PUT, DELETE/archive | `/api/admin/website/projects` |
| News | GET, POST, PUT, DELETE/archive | `/api/admin/website/news` |
| Events | GET, POST, PUT, DELETE/archive | `/api/admin/website/events` |
| Welfare | GET, POST, PUT, DELETE/archive | `/api/admin/website/welfare` |
| Leadership | GET, POST, PUT, DELETE/archive | `/api/admin/website/leadership` |
| Navigation | GET, PUT | `/api/admin/website/navigation` |
| Footer | GET, PUT | `/api/admin/website/footer` |
| Settings | GET, PUT | `/api/admin/website/settings` |
| SEO/social | GET, PUT | `/api/admin/website/seo` |
| Email templates | GET, PUT, reset | `/api/admin/website/email-templates` |
| Contact messages | GET, PATCH | `/api/admin/website/contact-messages` |

Write operations should support `draft`, `published`, `scheduled`, `archived`, `visibility`, `featured`, `sort_order`, `published_at`, and optimistic concurrency where available. Never permit arbitrary server-side code in email bodies or content fields.

## Leadership and profile images

Leadership records should reference an existing Laravel user/member (`member_id`) and expose only approved public fields: `public_name`, `public_title`, `public_bio`, `sort_order`, `published`, and a safe profile image URL from the existing profile image system. Public visibility must be an explicit CMS permission. Do not copy private member images, create a second user table, or expose private profile fields.

## SEO/media fields

Every public entity should support `seo_title`, `meta_description`, `og_title`, `og_description`, `og_image_id`, `canonical_url`, and `robots`. The API should return a normalized `seo` object so the presentation layer can generate page metadata consistently.

## Error and empty states

Use standard JSON errors: `{ "message": "...", "errors": { "field": ["..."] }, "reference": "..." }`. Public endpoints should return safe generic messages. The frontend must handle loading, empty, validation and unavailable states without exposing server details.

## Adapter shape

Recommended Laravel adapter methods: `getHomepage()`, `getPage(slug)`, `getProjects(filters)`, `getProject(slug)`, `getNews(filters)`, `getEvents(filters)`, `getLeadership()`, `getGallery(filters)`, `getResources(filters)`, `getSettings()`, `sendContactEnquiry(payload)`, and `getRegistrationHandoff()`. Keep transport/auth concerns inside the adapter so public UI can later be ported to Blade/components without changing content structure.
