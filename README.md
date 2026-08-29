# DaoTalk server version

## Repository contents
- `index.html` — public site
- `admin.html` — admin panel
- `api/` — PHP API
- `api/config.example.php` — configuration template

## Server-only file
Create `api/config.php` on the server with the database password. It is ignored by Git.

## Database
MySQL/Percona database:
- host: localhost
- database: u3385058_daotalk_db
- user: u3385058_daotalk

Tables:
- teachers
- content
- requests

## Important
Do not put the real database password in GitHub.
