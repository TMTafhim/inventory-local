# AGENTS.md — Sigma Royal Inventory System

## Quick start
- **Stack**: Plain PHP (no framework), MariaDB, AdminLTE 3, jQuery, DataTables
- **Entry**: `index.php` → `FileContent.Value.php` → auth check → `SessionController.php` → header → page body → footer
- **Serve**: XAMPP / Apache, point doc root here. No npm/dev server needed for app pages.

## Routing
- URL page is derived from the query string: `?Product_Information` → loads `CommonPagE/Product_Information.Doel.php`
- URL segments: `$page_title` (first segment), `$MenuName` (second), `$DocumentData` (third), `$PageStatusCheck` (fourth)
- Public login: `Fontend.DataValue.php` handles login POST; `Login.Doel.php` is the login page

## Database
- **Host**: `localhost`, **DB**: `sigma_inventory`, **User**: `sigma_inventory`, **Pass**: `inventory@#102030`
- Config in `BDB/DBConnEction.php` — hardcoded, not env-based
- `$pdo` is a global PDO instance (set in DBConnEction, available to all includes)
- Full DB dump: `BDB/sigma_inventory.sql` (~3.2M lines, MariaDB)
- Migrations: `BDB/migrations/` (currently empty directory)

## Architecture
- **No framework** — flat `include()`-based composition
- **Auth**: Session-based (`$_SESSION['LoginReGiSterSession']` = employee ID). Auth functions in `BDB/Auth.php`
- **Super admin**: hardcoded to employee ID `121` (`authIsSuperAdmin()`)
- **Permissions**: `$menu_access` (JSON array from `employee_information.menu_access`) + `$role_permission` string; admins bypass menu checks
- **CRUD handlers**: `BackendInsert.DataValue.php`, `BackendUPDATE.DataValue.php`, `BackendDelete.DataValue.php` (each inspects `$_POST` keys)
- **Audit**: Auto-logged via `BDB/ActivityAudit.php` to `system_activity_log`; auto-prunes after 15 days

## Key files & directories
| Path | Purpose |
|---|---|
| `CommonPagE/*.Doel.php` | Authenticated page templates |
| `Backend*.DataValue.php` | Insert/Update/Delete handlers |
| `BDB/DBConnEction.php` | DB connection + `$pdo`, URL routing |
| `BDB/Auth.php` | `authIsSuperAdmin()`, `authCanEditRequisitionHistory()` |
| `BDB/ActivityAudit.php` | Activity audit logging + auto-prune |
| `Fontend.DataValue.php` | Login POST handler |
| `SessionController.php` | URL parsing (`$page_title`, `$MenuName`, etc.) |
| `LeftSide.Data.php` | Sidebar menu + pending counts |
| `plugins/` | All frontend assets (AdminLTE, jQuery, DataTables, etc.) |
| `dist/` | Compiled AdminLTE CSS/JS |
| `build/` | AdminLTE build tooling (Rollup, SCSS, Babel) |

## Frontend conventions
- DataTables with `id="example1"` / `id="example2"` — configured in `FooterScript.Data.php`
- Column auto-classing from header text (`.doc-col-qty`, `.doc-col-amount`, etc.)
- Enhanced search bar with column scoping + product autocomplete
- Custom alert dialogs via `appShowAlert(msg, type, title)` replacing native `alert()`
- Toastr notifications (configured in `HeaderScript.Data.php`)
- Jodit editor on elements `area_editor1` through `area_editor4`

## PHP conventions
- All `<?php` tags, no short tags
- `$page_title` and `$MenuName` are derived from URL (global scope)
- Global `$pdo`, `$base_url`, `$current_date`, `$current_time` available in all includes
- File uploads stored in `HRPhoto/`, `HRCV/`, `Signature/`, `image/`
- Password hashing uses `password_hash()` / `password_verify()`
- CSRF token for requisition approval forms via `requestionApprovalCsrfToken()` / `requestionApprovalVerifyCsrf()`
- All SQL queries use `$pdo->query()` (direct) or `$pdo->prepare()` + execute parameterized

## Testing
- No test framework or test scripts found
- Only dependency: `picqer/php-barcode-generator` v2.2.4 (Composer)
- Verify changes by opening the app in a browser after serving via XAMPP/Apache
