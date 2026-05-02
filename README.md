# SaaS School Management System (ERP + Website Builder)

Multi-tenant SaaS platform for managing schools, built with **Laravel API** + **React SPA**.

## 📁 Project Structure

```
sms_erp/
├── backend/              # Laravel 12 API
│   ├── app/              # Models, Controllers, Middleware
│   ├── config/           # App configuration
│   ├── database/         # Migrations, Seeders
│   ├── routes/           # API routes
│   ├── public/           # Entry point
│   └── .env              # Environment variables
│
├── frontend/             # React 19 + Vite
│   ├── src/
│   │   ├── config/       # API config, subdomain helper
│   │   ├── context/      # AuthContext (global state)
│   │   ├── layouts/      # Admin, School, Portal layouts
│   │   ├── pages/        # Route pages by section
│   │   └── router/       # React Router config
│   ├── public/
│   └── .env
│
├── docker/               # Docker configuration
│   ├── backend/          # Laravel Dockerfile
│   ├── frontend/         # React Dockerfile + nginx
│   ├── nginx/            # Nginx configs (SPA + API)
│   └── docker-compose.yml
│
├── deploy/               # Deployment scripts
│   ├── backend.sh        # Deploy Laravel
│   ├── frontend.sh       # Deploy React
│   └── docker.sh         # Docker deploy
│
└── .env.example          # Root environment template
```

## 🚀 Quick Start

### Prerequisites
- PHP 8.2+
- Composer
- Node.js 18+
- MySQL 8.0
- XAMPP (recommended for Windows)

### Local Development

#### 1. Backend (Laravel)

```bash
cd backend

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Create database (MySQL)
# Using XAMPP: C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE sms_erp;"

# Run migrations
php artisan migrate

# Seed roles, plans, and super admin
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=PlanSeeder

# Start server
php artisan serve
```

API runs at: `http://localhost:8000`

#### 2. Frontend (React)

```bash
cd frontend

# Install dependencies
npm install

# Start dev server
npm run dev
```

App runs at: `http://localhost:5173`

## 📡 API Endpoints

### Authentication
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/auth/register` | Register user |
| POST | `/api/auth/login` | Login |
| POST | `/api/auth/logout` | Logout (auth) |
| GET | `/api/auth/user` | Get current user (auth) |

### Schools
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/schools/register` | Register new school + admin |
| GET | `/api/admin/schools` | List schools (super admin) |
| POST | `/api/admin/schools` | Create school (super admin) |
| GET | `/api/admin/schools/{id}` | View school (super admin) |
| PUT | `/api/admin/schools/{id}` | Update school (super admin) |
| DELETE | `/api/admin/schools/{id}` | Delete school (super admin) |
| GET | `/api/admin/schools/stats` | School statistics (super admin) |

### Plans
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/plans` | List plans |
| GET | `/api/plans/{id}` | View plan |
| GET/POST/PUT/DELETE | `/api/admin/plans` | Manage plans (super admin) |

## 🔐 Default Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | `superadmin@admin.com` | `admin123` |

## 🌐 Frontend Routes

| URL | Purpose | Access |
|-----|---------|--------|
| `/` | Public website | Everyone |
| `/super-admin/login` | Super Admin login | Super Admin |
| `/super-admin` | Super Admin dashboard | Super Admin |
| `/super-admin/schools` | Manage schools | Super Admin |
| `/school-admin/login` | School Admin login | School Admin |
| `/school-admin` | School ERP dashboard | School Admin / Teacher |
| `/portal` | Student/Parent portal | Student / Parent |
| `/register-school` | Register new school | Public |

## 🐳 Docker Deployment

```bash
# Copy environment
cp .env.example .env

# Start all services
docker-compose up -d --build

# Run migrations
docker-compose exec api php artisan migrate --force

# Seed database
docker-compose exec api php artisan db:seed
```

## 📦 Production Deployment

### Option 1: Docker (Recommended)
```bash
bash deploy/docker.sh
```

### Option 2: Manual Deploy
```bash
# Deploy backend
bash deploy/backend.sh

# Deploy frontend
bash deploy/frontend.sh
```

### Option 3: VPS (Forge / Ploi)
1. Point domain to server
2. Clone repository
3. Run `composer install --no-dev`
4. Run `npm ci && npm run build`
5. Configure Nginx to serve `frontend/dist/`
6. Configure Nginx to proxy `/api/` to Laravel
7. Set up SSL (Let's Encrypt)

## 🏗️ Multi-Tenant Architecture

### Subdomain Detection
- `school1.yourdomain.com` → School 1
- `school2.yourdomain.com` → School 2
- Middleware: `DetectTenant` detects school from subdomain

### Data Isolation
- `BelongsToTenant` trait auto-scopes queries by `school_id`
- Each school's data is completely isolated
- Super admin has access to all schools

## 🛠️ Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend | Laravel 12 |
| Frontend | React 19 + Vite |
| Database | MySQL 8.0 |
| Cache/Queue | Redis |
| Auth | Laravel Sanctum |
| RBAC | Spatie Laravel Permission |
| Deployment | Docker / Docker Compose |
