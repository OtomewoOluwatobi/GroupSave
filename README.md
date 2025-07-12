# GroupSave - Laravel-based Group Savings Platform

## Overview

GroupSave is a Laravel-based web application that enables users to create and manage group savings plans. The platform allows users to form savings groups, invite members, set contribution amounts, and manage group finances collaboratively.

## Features

### Core Functionality
- **User Authentication**: JWT-based authentication system
- **Group Management**: Create, manage, and participate in savings groups
- **Member Invitations**: Email-based invitation system for group members
- **Dashboard**: Comprehensive user dashboard with group statistics
- **Role-based Access**: Admin and member roles within groups

### Key Components
- **Groups**: Savings groups with target amounts and payment schedules
- **Members**: User management within groups
- **Invitations**: Email-based member invitation system
- **Payments**: Scheduled payment tracking
- **Notifications**: Email notifications for group activities

## Technical Stack

### Backend
- **Framework**: Laravel 12.x
- **PHP Version**: 8.2+
- **Database**: MySQL
- **Authentication**: JWT (tymon/jwt-auth)
- **API Documentation**: Swagger (darkaonline/l5-swagger)
- **Permissions**: Spatie Laravel Permission

### Key Dependencies
```json
{
  "laravel/framework": "^12.0",
  "laravel/sanctum": "^4.0",
  "tymon/jwt-auth": "^2.2",
  "spatie/laravel-permission": "^6.16",
  "darkaonline/l5-swagger": "^9.0"
}
```

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL
- Node.js (for frontend assets)

### Setup Instructions

1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd GroupSave
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **JWT Setup**
   ```bash
   php artisan jwt:secret
   ```

6. **Start Development Server**
   ```bash
   php artisan serve
   ```

## Configuration

### Environment Variables

#### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=groupsave_dbase
DB_USERNAME=root
DB_PASSWORD=password
```

#### Mail Configuration
```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@groupsave.com
```

#### JWT Configuration
```env
JWT_SECRET=your_jwt_secret_key
```

#### SMS Configuration (Termii)
```env
TERMII_API_KEY=your_termii_api_key
TERMII_BASE_URL=https://api.ng.termii.com/api/sms/send
TERMII_COUNTRY_CODE=234
TERMII_DEFAULT_SENDER=N-Alert
```

## API Documentation

### Authentication Endpoints
- `POST /api/auth/login` - User login
- `POST /api/auth/register` - User registration
- `POST /api/auth/logout` - User logout
- `POST /api/auth/refresh` - Refresh JWT token

### Group Management
- `GET /api/groups` - List user's groups
- `POST /api/groups` - Create new group
- `GET /api/groups/{id}` - Get group details
- `PUT /api/groups/{id}` - Update group
- `DELETE /api/groups/{id}` - Delete group

### Group Invitations
- `POST /api/groups/{id}/invite` - Invite members
- `GET /api/groups/pending-invitations` - Get pending invitations
- `POST /api/groups/{id}/accept-invitation` - Accept invitation

### User Dashboard
- `GET /api/user/dashboard` - Get user dashboard data
- `GET /api/user/profile` - Get user profile
- `PUT /api/user/profile` - Update user profile

## Database Schema

### Core Tables

#### Users
- `id` (Primary Key)
- `name` (VARCHAR)
- `email` (VARCHAR, Unique)
- `mobile` (VARCHAR)
- `password` (VARCHAR)
- `created_at`, `updated_at`

#### Groups
- `id` (Primary Key)
- `title` (VARCHAR)
- `total_users` (INTEGER)
- `target_amount` (DECIMAL)
- `payable_amount` (DECIMAL)
- `expected_start_date` (DATE)
- `expected_end_date` (DATE)
- `payment_out_day` (INTEGER)
- `owner_id` (Foreign Key → Users)
- `status` (ENUM: active, completed, cancelled)
- `created_at`, `updated_at`

#### Group_User (Pivot Table)
- `id` (Primary Key)
- `group_id` (Foreign Key → Groups)
- `user_id` (Foreign Key → Users)
- `role` (ENUM: admin, member)
- `is_active` (BOOLEAN)
- `created_at`, `updated_at`

## Key Features Implementation

### Group Creation
```php
public function store(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'total_users' => 'required|integer|min:2|max:300',
        'target_amount' => 'required|numeric|min:0',
        'expected_start_date' => 'required|date|after:today',
        'payment_out_day' => 'required|integer|min:1|max:31',
        'membersEmails' => 'required|array',
        'membersEmails.*' => 'required|email'
    ]);

    $group = DB::transaction(function () use ($validated) {
        $group = Group::create($validated);
        $group->users()->attach(Auth::id(), ['role' => 'admin']);
        $this->inviteMembers($group, $validated['membersEmails']);
        return $group;
    });

    return response()->json(['message' => 'Group created successfully', 'data' => $group], 201);
}
```

### Email Invitations
```php
private function inviteMembers(Group $group, array $emails)
{
    $creatorEmail = Auth::user()->email;
    $memberEmails = array_filter($emails, fn($email) => $email !== $creatorEmail);

    foreach ($memberEmails as $email) {
        $user = User::firstOrCreate(['email' => $email]);
        $group->users()->attach($user->id, ['role' => 'member', 'is_active' => false]);
        Mail::to($email)->send(new GroupInvitation($group, $user));
    }
}
```

## Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit

# Run tests with coverage
php artisan test --coverage
```

### Test Configuration
The application uses PHPUnit for testing with the following configuration:
- Test database: SQLite (in-memory)
- Mail driver: Array (for testing)
- Queue connection: Sync

## Deployment

### Production Setup

1. **Environment Configuration**
   ```bash
   APP_ENV=production
   APP_DEBUG=false
   ```

2. **Optimize Application**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan optimize
   ```

3. **Database Migration**
   ```bash
   php artisan migrate --force
   ```

### Docker Deployment
The application includes Docker configuration for containerized deployment:
```bash
docker-compose up -d
```

## Security Features

### Authentication
- JWT token-based authentication
- Token refresh mechanism
- Password hashing using bcrypt

### Authorization
- Role-based access control
- Group ownership verification
- Member invitation validation

### Data Protection
- Input validation and sanitization
- SQL injection prevention
- XSS protection
- CSRF protection

## API Rate Limiting

The application implements rate limiting to prevent abuse:
- Authentication endpoints: 5 requests per minute
- API endpoints: 60 requests per minute
- Email sending: 10 requests per minute

## Error Handling

### Global Exception Handler
```php
public function render($request, Throwable $exception)
{
    if ($request->expectsJson()) {
        return response()->json([
            'message' => 'Something went wrong',
            'error' => $exception->getMessage()
        ], 500);
    }

    return parent::render($request, $exception);
}
```

### Validation Errors
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": ["The email field is required."]
    }
}
```

## Logging

### Log Channels
- `stack`: Default logging stack
- `single`: Single file logging
- `database`: Database logging
- `mail`: Email error notifications

### Log Levels
- `emergency`: System unusable
- `alert`: Action must be taken immediately
- `critical`: Critical conditions
- `error`: Error conditions
- `warning`: Warning conditions
- `notice`: Normal but significant condition
- `info`: Informational messages
- `debug`: Debug-level messages

## Performance Optimization

### Database Optimization
- Eloquent query optimization
- Eager loading relationships
- Database indexing
- Query caching

### Caching Strategy
- Database cache driver
- Configuration caching
- Route caching
- View caching

## Contributing

### Development Workflow
1. Fork the repository
2. Create a feature branch
3. Make changes and add tests
4. Run test suite
5. Submit pull request

### Code Standards
- PSR-12 coding standards
- Laravel best practices
- PHPDoc documentation
- Type hints and return types

### Git Workflow
```bash
# Feature branch
git checkout -b feature/new-feature

# Commit changes
git commit -m "Add new feature"

# Push to remote
git push origin feature/new-feature

# Create pull request
```

## Troubleshooting

### Common Issues

#### Database Connection
```bash
php artisan config:clear
php artisan migrate:status
```

#### JWT Token Issues
```bash
php artisan jwt:secret
php artisan config:cache
```

#### Mail Configuration
```bash
php artisan config:clear
php artisan queue:work
```

## License

This project is open-sourced software licensed under the MIT license.

## Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the documentation

---

**Last Updated**: July 2025
**Version**: 1.0.0
**Laravel Version**: 12.x
**PHP Version**: 8.2+
