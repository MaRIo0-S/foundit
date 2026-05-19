# Laravel + PostgreSQL + Inertia + Vue 3 + Pinia + SCSS
### Local Setup Without Docker — Battle-Tested (2026)

**Stack:**
- Laravel 11
- PostgreSQL (installed locally)
- Inertia.js v2
- Vue 3 (Composition API)
- Pinia
- SCSS / Sass
- Vite
- Pure JavaScript (no TypeScript, no Tailwind, no shadcn)

**No Docker. No Sail. Everything runs directly on your machine.**

---

## PART 1 — Prerequisites

Install these on your Ubuntu machine before starting. If you already have them, skip ahead.

---

### PHP 8.3+

```bash
sudo apt update
sudo apt install -y php php-cli php-mbstring php-xml php-bcmath php-curl php-zip php-pgsql php-pdo
```

Verify:
```bash
php -v
```

> ⚠️ **`php-pgsql` and `php-pdo` are required.** Without them, Laravel cannot connect to PostgreSQL  
> and you get `could not find driver` errors that are hard to trace back to a missing extension.

---

### Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

Verify:
```bash
composer -V
```

---

### Node.js + npm

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

Verify:
```bash
node -v
npm -v
```

---

### PostgreSQL

```bash
sudo apt install -y postgresql postgresql-contrib
```

Start and enable the service:
```bash
sudo systemctl start postgresql
sudo systemctl enable postgresql
```

Verify it's running:
```bash
sudo systemctl status postgresql
```

---

## PART 2 — First-Time Project Creation

> **Only do this section once**, when creating the template from scratch.  
> If you're cloning an existing repo, skip to **Part 3**.

---

### Step 1 — Create the Laravel project

```bash
composer create-project laravel/laravel my-project
cd my-project
```

---

### Step 2 — Create a PostgreSQL database and user

Switch to the postgres system user and open the PostgreSQL shell:

```bash
sudo -u postgres psql
```

Inside the shell, run:

```sql
CREATE DATABASE my_project;
CREATE USER myuser WITH PASSWORD 'mypassword';
GRANT ALL PRIVILEGES ON DATABASE my_project TO myuser;
\q
```

> ⚠️ Remember what you set here — you'll need these values in `.env`.

---

### Step 3 — Configure `.env`

Open `.env` and replace the database section:

```env
APP_NAME=MyProject
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=my_project
DB_USERNAME=myuser
DB_PASSWORD=mypassword
```

> ⚠️ **`DB_HOST` is `127.0.0.1` here, not `pgsql`.**  
> `pgsql` is a Docker service name — it only works inside Docker networks.  
> Without Docker, PostgreSQL runs on your local machine and is reached via `127.0.0.1`.

---

### Step 4 — Generate app key

```bash
php artisan key:generate
```

> ⚠️ This is mandatory. Without it sessions, cookies, and auth will all break.

---

### Step 5 — Install Inertia server-side

```bash
composer require inertiajs/inertia-laravel
```

Generate the middleware:

```bash
php artisan inertia:middleware
```

Register it in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\HandleInertiaRequests::class,
    ]);
})
```

---

### Step 6 — Install frontend dependencies

```bash
npm install vue @inertiajs/vue3 pinia
npm install -D @vitejs/plugin-vue sass
```

---

### Step 7 — Configure Vite

Replace `vite.config.js` entirely:

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/scss/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        vue(),
    ],

    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
})
```

---

### Step 8 — Create the SCSS structure

```bash
mkdir -p resources/scss/base
touch resources/scss/app.scss
touch resources/scss/base/_reset.scss
touch resources/scss/base/_variables.scss
touch resources/scss/base/_global.scss
```

**`resources/scss/base/_reset.scss`**
```scss
*,
*::before,
*::after {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
```

**`resources/scss/base/_variables.scss`**
```scss
// Colors
$primary:    #2563eb;
$secondary:  #64748b;
$danger:     #dc2626;
$success:    #16a34a;
$white:      #ffffff;
$black:      #0f172a;

// Layout
$radius:     8px;
$shadow:     0 2px 12px rgba(0, 0, 0, 0.08);
$transition: 0.3s ease;

// Typography
$font-base:  'Arial', sans-serif;
$font-size:  16px;
```

**`resources/scss/base/_global.scss`**
```scss
@use 'variables' as *;

body {
    font-family: $font-base;
    font-size: $font-size;
    background: #f8fafc;
    color: #1e293b;
    min-height: 100vh;
}

a {
    color: $primary;
    text-decoration: none;

    &:hover {
        text-decoration: underline;
    }
}

.container {
    width: min(1200px, 90%);
    margin: 0 auto;
}
```

**`resources/scss/app.scss`**
```scss
@use './base/reset';
@use './base/variables';
@use './base/global';

// Add component-level SCSS imports below as your project grows:
// @use './components/button';
// @use './components/card';
// @use './pages/home';
```

---

### Step 9 — Create the Blade root template

Delete `resources/views/welcome.blade.php`, then create:

**`resources/views/app.blade.php`**
```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'App') }}</title>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>
```

---

### Step 10 — Bootstrap Vue + Inertia + Pinia

Replace `resources/js/app.js` entirely:

```js
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { createPinia } from 'pinia'

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        const page = pages[`./Pages/${name}.vue`]

        if (!page) {
            console.error(`[Inertia] Page not found: "${name}"`)
            console.error('[Inertia] Available pages:', Object.keys(pages))
        }

        return page
    },

    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(createPinia())
            .mount(el)
    },
})
```

> ⚠️ **The folder name is `Pages` (capital P).** Linux is case-sensitive.  
> `Pages/Home.vue` and `pages/Home.vue` are completely different paths.

---

### Step 11 — Create the Pages folder and first page

```bash
mkdir -p resources/js/Pages
```

**`resources/js/Pages/Home.vue`**
```vue
<template>
    <div class="home">
        <div class="container">
            <h1>Laravel + Inertia + Vue is working</h1>
            <p>Count: {{ counter.count }}</p>

            <div class="actions">
                <button @click="counter.increment">+</button>
                <button @click="counter.decrement">−</button>
                <button @click="counter.reset">Reset</button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useCounterStore } from '@/stores/counter'

const counter = useCounterStore()
</script>

<style scoped lang="scss">
.home {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;

    h1 {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    p {
        font-size: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .actions {
        display: flex;
        gap: 1rem;

        button {
            padding: 10px 24px;
            border: none;
            border-radius: 8px;
            background: #2563eb;
            color: white;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.2s ease;

            &:hover {
                background: #1d4ed8;
                transform: translateY(-1px);
            }
        }
    }
}
</style>
```

---

### Step 12 — Create the Pinia store

```bash
mkdir -p resources/js/stores
```

**`resources/js/stores/counter.js`**
```js
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useCounterStore = defineStore('counter', () => {
    const count = ref(0)

    const increment = () => count.value++
    const decrement = () => count.value--
    const reset    = () => count.value = 0

    return { count, increment, decrement, reset }
})
```

---

### Step 13 — Set up the route

**`routes/web.php`**
```php
<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home');
});
```

---

### Step 14 — Run database migrations

```bash
php artisan migrate
```

---

### Step 15 — Start the development servers

You need two terminals running at the same time:

**Terminal 1 — Laravel:**
```bash
php artisan serve
```

**Terminal 2 — Vite:**
```bash
npm run dev
```

Open your browser at `http://localhost:8000`.

> ⚠️ **Both must be running at the same time.** Closing either one breaks the app.  
> Laravel serves the backend. Vite serves the frontend assets.  
> If Vite is not running, you get `ViteManifestNotFoundException`.

---

## PART 3 — Reusing the Template (Cloning from GitHub)

```bash
git clone https://github.com/your-username/your-template.git new-project
cd new-project
```

Then in this exact order:

```bash
# 1. Copy environment file
cp .env.example .env

# 2. Edit .env — set your local DB credentials
nano .env

# 3. Install PHP dependencies
composer install

# 4. Generate app key
php artisan key:generate

# 5. Create the database in PostgreSQL
sudo -u postgres psql -c "CREATE DATABASE new_project;"
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE new_project TO myuser;"

# 6. Run migrations
php artisan migrate

# 7. Install JS dependencies
npm install

# 8. Start both servers (two terminals)
php artisan serve   # Terminal 1
npm run dev         # Terminal 2
```

> ⚠️ **Unlike the Docker version, there is no `.env` port override system here.**  
> If port 8000 is taken, change it when starting the server:
> ```bash
> php artisan serve --port=8001
> ```

---

## PART 4 — Pushing to GitHub

Identical to the Docker version. Make sure `.gitignore` excludes:

```
/vendor
/node_modules
/.env
/storage/logs/*.log
/public/build
/public/hot
```

```bash
git init
git add .
git commit -m "Initial template: Laravel + PostgreSQL + Inertia + Vue + SCSS"
git remote add origin https://github.com/your-username/your-repo.git
git branch -M main
git push -u origin main
```

> ⚠️ Never push `.env`. Push `.env.example` with empty values instead.

---

## PART 5 — The `.env.example` for this setup

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# --- Database (local PostgreSQL) ---
# DB_HOST is 127.0.0.1 here — NOT "pgsql" (that's Docker only)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=your_pg_user
DB_PASSWORD=your_pg_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
```

---

## PART 6 — Folder Structure

```
project/
├── app/
├── bootstrap/
├── database/
│   └── migrations/
├── resources/
│   ├── js/
│   │   ├── Pages/           ← Vue pages (capital P, always)
│   │   │   └── Home.vue
│   │   ├── stores/          ← Pinia stores
│   │   │   └── counter.js
│   │   └── app.js           ← Inertia bootstrap
│   ├── scss/
│   │   ├── app.scss         ← Main entry (imported by Vite)
│   │   └── base/
│   │       ├── _reset.scss
│   │       ├── _variables.scss
│   │       └── _global.scss
│   └── views/
│       └── app.blade.php    ← Single Blade file, Inertia root
├── routes/
│   └── web.php
├── .env
├── .env.example
├── .gitignore
├── vite.config.js
├── package.json
└── composer.json
```

---

## PART 7 — Troubleshooting Reference

---

### `could not find driver (pgsql)`

The PHP PostgreSQL extension is not installed.

```bash
sudo apt install -y php-pgsql php-pdo
sudo systemctl restart php8.3-fpm   # adjust version if needed
```

Then retry your artisan command.

---

### `SQLSTATE[08006] connection refused` or `could not connect to server`

PostgreSQL is not running.

```bash
sudo systemctl start postgresql
sudo systemctl status postgresql
```

---

### `FATAL: database "x" does not exist`

You forgot to create the database, or it was dropped.

```bash
sudo -u postgres psql -c "CREATE DATABASE your_db_name;"
sudo -u postgres psql -c "GRANT ALL PRIVILEGES ON DATABASE your_db_name TO your_user;"
php artisan migrate
```

---

### `FATAL: role "x" does not exist`

The PostgreSQL user in your `.env` doesn't exist.

```bash
sudo -u postgres psql
```

Inside psql:
```sql
CREATE USER myuser WITH PASSWORD 'mypassword';
GRANT ALL PRIVILEGES ON DATABASE my_project TO myuser;
\q
```

---

### `ViteManifestNotFoundException`

Vite is not running.

```bash
npm run dev
```

Keep this terminal open the entire time you develop.

---

### Blank / white page

Same cause as the Docker version — Inertia cannot resolve the Vue page.

1. Does `resources/js/Pages/Home.vue` exist?
2. Is the folder named `Pages` (capital P)?
3. Does `Inertia::render('Home')` match the filename?
4. Check browser DevTools → Console for the exact error.

---

### `Cannot read properties of null (reading 'component')`

The glob pattern in `app.js` can't find the page file. Check for case mismatch:

```bash
ls resources/js/Pages
```

If it shows `pages` (lowercase), rename it:
```bash
mv resources/js/pages resources/js/Pages
```

---

### Port 8000 already in use

```bash
php artisan serve --port=8001
```

Update `APP_URL` in `.env` to match:
```env
APP_URL=http://localhost:8001
```

---

## PART 8 — Daily Development Workflow

Every day when you start working:

```bash
# Make sure PostgreSQL is running
sudo systemctl start postgresql

# Terminal 1 — Laravel backend
php artisan serve

# Terminal 2 — Vite frontend (keep open)
npm run dev
```

When you're done, just close both terminals. Unlike Docker, there's nothing to explicitly stop — PostgreSQL runs as a system service.

---

## Quick Command Reference

```bash
# Laravel
php artisan serve
php artisan serve --port=8001
php artisan migrate
php artisan migrate:fresh
php artisan migrate:fresh --seed
php artisan make:controller UserController
php artisan make:model Product -m
php artisan route:list
php artisan optimize:clear

# Composer
composer require vendor/package
composer install

# NPM / Vite
npm install
npm run dev
npm run build

# PostgreSQL
sudo systemctl start postgresql
sudo systemctl stop postgresql
sudo systemctl status postgresql
sudo -u postgres psql                          # open psql as postgres superuser
sudo -u postgres psql -d your_database         # open specific database
psql -U myuser -d my_project -h 127.0.0.1     # connect as your app user
```

---

## Docker vs No-Docker — Key Differences

| Thing                  | Docker (Sail)             | No Docker (Local)         |
|------------------------|---------------------------|---------------------------|
| `DB_HOST`              | `pgsql`                   | `127.0.0.1`               |
| Start backend          | `sail up -d`              | `php artisan serve`       |
| Start frontend         | `sail npm run dev`        | `npm run dev`             |
| Run migrations         | `sail artisan migrate`    | `php artisan migrate`     |
| Install packages       | `sail composer require`   | `composer require`        |
| Install npm packages   | `sail npm install`        | `npm install`             |
| Stop everything        | `sail down`               | Close terminals           |
| Port conflicts         | Common, use `FORWARD_*`   | Rare, use `--port=`       |
| PostgreSQL location    | Inside container          | System service            |
| PHP version            | Inside container          | Whatever is installed     |
