# Task Management System

A modern task management system built with Laravel, Inertia.js, Vue.js, and Bootstrap. Features real-time updates using Laravel Echo and Pusher.

## Features

- Create, read, update, and delete tasks
- Real-time updates using Laravel Echo and Pusher
- Task filtering by status and priority
- Responsive design with Bootstrap
- RESTful API endpoints
- Comprehensive test coverage

## Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js and NPM
- MySQL/PostgreSQL
- Pusher account (for real-time features)

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
cd [project-directory]
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install JavaScript dependencies:
```bash
npm install
```

4. Create environment file:
```bash
cp .env.example .env
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Configure your database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

7. Configure Pusher in `.env`:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
```

## Database Setup

1. Run migrations and seeders:
```bash
php artisan migrate:fresh --seed
```

This will:
- Create all necessary database tables
- Create a test user (email: test@example.com)
- Seed sample tasks with different statuses and priorities

## Development

1. Start Laravel development server:
```bash
php artisan serve
```

2. Start Vite development server:
```bash
npm run dev
```

3. Start Laravel Echo server (for real-time features):
```bash
php artisan websockets:serve
```

## Testing

Run tests using Pest PHP:

1. Run all tests:
```bash
composer test
```

2. Run specific test suites:
```bash
composer test:unit    # Run unit tests
composer test:feature # Run feature tests
```

3. Run tests with coverage:
```bash
composer test:coverage
```

4. Run tests in watch mode:
```bash
composer test:watch
```

## API Endpoints

### Tasks

- `GET /api/tasks` - List all tasks
- `POST /api/tasks` - Create a new task
- `GET /api/tasks/{id}` - Get a specific task
- `PUT /api/tasks/{id}` - Update a task
- `DELETE /api/tasks/{id}` - Delete a task

### Authentication

- `POST /api/login` - User login
- `POST /api/logout` - User logout

## Real-time Features

The application uses Laravel Echo and Pusher for real-time updates. When tasks are created, updated, or deleted, all connected clients will receive updates automatically.

### Echo Server Configuration

1. Install Laravel Websockets:
```bash
composer require beyondcode/laravel-websockets
```

2. Publish the configuration:
```bash
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider" --tag="config"
```

3. Start the WebSocket server:
```bash
php artisan websockets:serve
```

## Production Deployment

1. Build assets for production:
```bash
npm run build
```

2. Optimize Laravel:
```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License. 