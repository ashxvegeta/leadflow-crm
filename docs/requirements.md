# LeadFlow CRM — Requirements & Design (v1 draft)

> Status: **Draft**. Review, change anything that doesn't fit, then treat this as the source of truth.

## 1. Goal

A small internal CRM where an **admin** manages a sales team and **sales users** track their leads
from first contact to won/lost.

There is **no public registration**. Only an admin creates accounts.

---

## 2. Roles & permissions

| Action                              | Admin | Sales            |
|-------------------------------------|:-----:|:----------------:|
| Log in / log out                    | ✅    | ✅               |
| View dashboard                      | ✅ (all data) | ✅ (own data) |
| Manage users (create, edit, activate/deactivate) | ✅ | ❌ |
| Create lead                         | ✅    | ✅ (auto-assigned to self) |
| View leads                          | ✅ all | ✅ only assigned to them |
| Edit lead / change status           | ✅ all | ✅ own only     |
| Assign / reassign lead              | ✅    | ❌               |
| Delete lead                         | ✅    | ❌               |
| Add notes to a lead                 | ✅    | ✅ own leads     |
| Add / complete follow-ups           | ✅    | ✅ own leads     |

**Rule:** every permission is checked on the **server**. Hiding a button in the UI is not enough.

---

## 3. Scope

### v1 (build now)
1. **Auth**: login, logout, session security, CSRF on every form *(mostly done)*
2. **Layout**: navbar, footer, flash messages *(mostly done)*
3. **User management** (admin): list, create, edit, activate/deactivate
4. **Leads**: list (with search + status filter + pagination), create, view, edit, assign, delete
5. **Lead notes**: add and view a timeline of notes on the lead page
6. **Follow-ups**: schedule a follow-up date, mark it done, and list today's and overdue follow-ups
7. **Dashboard**: lead counts by status, my follow-ups today, recent leads

### v2 (later, not now)
- Change own password / profile page
- CSV import & export of leads
- Email notifications for follow-ups
- Activity log (who changed what)
- Reports/charts (conversion rate per sales user)
- "Forgot password" by email

---

## 4. Lead lifecycle

```
New → Contacted → Qualified → Proposal → Won
                                      ↘ Lost   (Lost can happen from any stage)
```

**Lead sources:** Website, Referral, Cold Call, Social Media, Event, Other

---

## 5. Database design

### `users` ✅ exists (`001_create_users_table.php`)
| Column     | Type                     | Notes |
|------------|--------------------------|-------|
| id         | INT UNSIGNED PK AI       | |
| name       | VARCHAR(100)             | |
| email      | VARCHAR(150) UNIQUE      | |
| password   | VARCHAR(255)             | bcrypt hash |
| role       | ENUM('admin','sales')    | default `sales` |
| is_active  | TINYINT(1)               | default 1 |
| created_at / updated_at | TIMESTAMP   | |

### `leads` (migration `002_create_leads_table.php`)
| Column      | Type                     | Notes |
|-------------|--------------------------|-------|
| id          | INT UNSIGNED PK AI       | |
| name        | VARCHAR(100) NOT NULL    | contact person |
| email       | VARCHAR(150) NULL        | |
| phone       | VARCHAR(20) NULL         | |
| company     | VARCHAR(150) NULL        | |
| source      | ENUM('website','referral','cold_call','social','event','other') | default `other` |
| status      | ENUM('new','contacted','qualified','proposal','won','lost') | default `new` |
| value       | DECIMAL(12,2) NULL       | expected deal value |
| assigned_to | INT UNSIGNED NULL → users.id | `ON DELETE SET NULL` |
| created_by  | INT UNSIGNED NOT NULL → users.id | |
| created_at / updated_at | TIMESTAMP    | |

Indexes: `status`, `assigned_to`, `email`

### `lead_notes` (migration `003_create_lead_notes_table.php`)
| Column     | Type                     | Notes |
|------------|--------------------------|-------|
| id         | INT UNSIGNED PK AI       | |
| lead_id    | INT UNSIGNED → leads.id  | `ON DELETE CASCADE` |
| user_id    | INT UNSIGNED → users.id  | who wrote it |
| note       | TEXT NOT NULL            | |
| created_at | TIMESTAMP                | |

### `followups` (migration `004_create_followups_table.php`)
| Column     | Type                     | Notes |
|------------|--------------------------|-------|
| id         | INT UNSIGNED PK AI       | |
| lead_id    | INT UNSIGNED → leads.id  | `ON DELETE CASCADE` |
| user_id    | INT UNSIGNED → users.id  | who must do it |
| due_at     | DATETIME NOT NULL        | |
| note       | VARCHAR(255) NULL        | e.g. "Call about pricing" |
| is_done    | TINYINT(1) DEFAULT 0     | |
| created_at | TIMESTAMP                | |

Index: `(user_id, is_done, due_at)` for the "my follow-ups today" query.

### Relationships
```
users 1 ──< leads        (assigned_to, created_by)
leads 1 ──< lead_notes   >── 1 users
leads 1 ──< followups    >── 1 users
```

> **Why we don't hard-delete users:** leads and notes reference them. To remove someone, **deactivate** them (`is_active = 0`) instead.

---

## 6. Routes / pages

| Method | URL                      | Controller@method          | Access |
|--------|--------------------------|----------------------------|--------|
| GET    | `/login`                 | AuthController@showLogin   | guest |
| POST   | `/login`                 | AuthController@login       | guest |
| POST   | `/logout`                | AuthController@logout      | auth |
| GET    | `/`                      | DashboardController@index  | auth |
| GET    | `/users`                 | UserController@index       | admin |
| GET    | `/users/create`          | UserController@create      | admin |
| POST   | `/users/store`           | UserController@store       | admin |
| GET    | `/users/edit?id=`        | UserController@edit        | admin |
| POST   | `/users/update`          | UserController@update      | admin |
| POST   | `/users/toggle`          | UserController@toggle      | admin |
| GET    | `/leads`                 | LeadController@index       | auth |
| GET    | `/leads/create`          | LeadController@create      | auth |
| POST   | `/leads/store`           | LeadController@store       | auth |
| GET    | `/leads/show?id=`        | LeadController@show        | auth (own or admin) |
| GET    | `/leads/edit?id=`        | LeadController@edit        | auth (own or admin) |
| POST   | `/leads/update`          | LeadController@update      | auth (own or admin) |
| POST   | `/leads/delete`          | LeadController@delete      | admin |
| POST   | `/leads/notes/store`     | LeadNoteController@store   | auth (own or admin) |
| POST   | `/followups/store`       | FollowupController@store   | auth (own or admin) |
| POST   | `/followups/done`        | FollowupController@done    | auth (own or admin) |
| GET    | `/followups`             | FollowupController@index   | auth |

> The router only matches exact paths, so IDs go in the query string (`?id=5`). Route parameters like `/leads/5` can come later.
> Everything that **changes data** uses `POST` + a CSRF token. That includes logout.

---

## 7. Non-functional rules (apply to every feature)

- **Security**: prepared statements only, `htmlspecialchars()` on all output, CSRF token on every POST,
  role/ownership check on every protected action, `session_regenerate_id()` on login.
- **Validation**: on the server. Show errors with flash messages and keep the old input.
- **UI**: Bootstrap 5, the shared layout (`layouts/header.php` + `footer.php`), usable on mobile.
- **Code**: one controller per module, no SQL inside views, small focused methods.
- **Git**: one branch per module, small commits, never commit broken code, and `.env` is never committed.

---

## 8. Build order (milestones)

| # | Milestone                              | Done when… |
|---|----------------------------------------|------------|
| 0 | Foundation cleanup                     | `requireAuth`/`requireAdmin` helpers exist, CSRF helper exists, logout uses POST, flash messages show in the layout |
| 1 | User management                        | Admin can list, create, edit and deactivate users, and sales users get a 403 |
| 2 | Leads CRUD                             | Create, list, view, edit and delete leads, with ownership rules enforced |
| 3 | Search, filter, pagination on leads    | Filter by status and assignee, search by name/email/company, 20 per page |
| 4 | Notes                                  | Timeline on the lead page, add a note |
| 5 | Follow-ups                             | Schedule, mark done, and see a "today + overdue" list |
| 6 | Dashboard                              | Counts by status, my follow-ups today, recent leads |
| 7 | Polish                                 | README setup guide, demo seeder (sales users + sample leads), screenshots |

After each milestone: test it manually against the checklist, commit, then push.
