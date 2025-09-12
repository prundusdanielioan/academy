# HLS Laravel App

A Laravel-based Learning Management System (LMS) with HLS video streaming capabilities.

## Features

- **Video Management**: Upload, transcode, and stream videos using HLS
- **User Authentication**: Secure login and registration system
- **Progress Tracking**: Track video viewing progress for users
- **Admin Panel**: Comprehensive admin dashboard for managing users and content
- **Role-Based Access**: User, Admin, and Superadmin roles with different permissions

## Installation

1. **Clone the repository**
```bash
git clone <repository-url>
cd hls-laravel-app
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database setup**
```bash
php artisan migrate
php artisan db:seed --class=SuperAdminSeeder
```

5. **Start the development server**
```bash
php artisan serve
```

## Admin System

### Default Superadmin Account
- **Email**: superadmin@lms.com
- **Password**: superadmin123
- **Role**: superadmin

⚠️ **Important**: Change the default password after first login!

### Creating Additional Admin Users

You can create additional admin users using the artisan command:

```bash
# Interactive mode
php artisan admin:create

# With parameters
php artisan admin:create --name="Admin User" --email="admin@example.com" --password="securepassword" --role="admin"
```

### Available Roles

- **user**: Regular user with basic access
- **admin**: Can access admin panel and manage content
- **superadmin**: Full system access (includes admin privileges)

### Admin Features

- **Dashboard**: Overview of system statistics
- **User Management**: View and manage all users
- **Video Management**: View and manage all videos across users
- **Role-based Navigation**: Admin links only appear for admin users

### Admin Routes

- `/admin/dashboard` - Admin dashboard
- `/admin/users` - User management
- `/admin/videos` - Video management

All admin routes are protected by the `admin` middleware and require authentication.

## API Endpoints

### Video Management
- `GET /videos` - List video-uri
- `GET /videos/create` - Formular upload
- `POST /videos` - Upload video
- `GET /videos/{id}` - Vizualizare video
- `GET /videos/{id}/edit` - Editare video
- `PUT /videos/{id}` - Actualizare video
- `DELETE /videos/{id}` - Ștergere video

### Progress Tracking
- `POST /videos/{id}/progress` - Actualizare progres
- `GET /videos/{id}/status` - Status video
- `GET /progress` - Progres utilizator

### Authentication
- `GET /login` - Pagină login
- `POST /login` - Autentificare
- `GET /register` - Pagină înregistrare
- `POST /register` - Înregistrare utilizator
- `POST /logout` - Deconectare

## Structura Proiectului

```
app/
├── Http/Controllers/
│   ├── VideoController.php          # Controller pentru video-uri
│   ├── AdminController.php          # Controller pentru admin panel
│   └── Auth/                        # Controller-e pentru autentificare
├── Models/
│   ├── Video.php                    # Model pentru video-uri
│   ├── VideoProgress.php            # Model pentru progres vizionare
│   └── User.php                     # Model pentru utilizatori
├── Services/
│   └── HlsTranscoderService.php     # Serviciu pentru transcodare HLS
└── Http/Requests/Auth/
    └── LoginRequest.php             # Request pentru login

resources/views/
├── layouts/
│   ├── app.blade.php                # Layout principal
│   └── navigation.blade.php         # Navigare
├── videos/
│   ├── index.blade.php              # Lista video-uri
│   ├── create.blade.php             # Upload video
│   ├── show.blade.php               # Player video
│   └── edit.blade.php               # Editare video
├── admin/
│   └── dashboard.blade.php          # Admin dashboard
├── auth/
│   ├── login.blade.php              # Pagină login
│   └── register.blade.php           # Pagină înregistrare
└── dashboard.blade.php              # Dashboard

database/migrations/
├── create_videos_table.php          # Migrație pentru video-uri
├── create_video_progress_table.php  # Migrație pentru progres
└── add_role_to_users_table.php      # Migrație pentru roluri
```

## Security

- All admin routes are protected by middleware
- Role-based access control implemented
- CSRF protection enabled
- Password hashing using Laravel's built-in hashing
- Input validation on all forms

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License.
# Trigger deployment
