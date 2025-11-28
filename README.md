# Circuits API

Backend API for Circuits, a grid-based programming puzzle game designed to teach programming logic through interactive challenges.

## About the Project

This is the **API and administration backend** for Circuits, an educational game where players control a robot navigating through circuit boards. This API handles all game logic, level creation, player management, and scoring.

The API provides:
- **Level Management**: Create, edit, and manage puzzle levels with configurable grids
- **Player Profiles**: User authentication and player profile management
- **Scoring System**: Track player achievements and XP
- **Admin Panel**: Web-based administration interface for game content management

### API Features

-   **RESTful Endpoints**: Clean API design for game client integration
-   **Player Management**: User authentication, player profiles with nickname and XP tracking
-   **Level CRUD Operations**: Create, read, update, and delete game levels
-   **Difficulty System**: Support for multiple difficulty levels (easy, medium, hard)
-   **Score Tracking**: Record player achievements with XP calculation based on efficiency
-   **Admin Interface**: Web-based UI for content management and moderation

### Database Schema

The API uses a relational database with the following main entities:

-   **Users**: Authentication and account management (with admin capabilities)
-   **Players**: Game profiles (one-to-one with users) tracking nickname and XP
-   **Levels**: Puzzle configurations with grid layout, difficulty, and constraints
-   **Tiles**: Building blocks for levels (empty, circuit, obstacle types)
-   **Scores**: Player achievements on completed levels
-   **Media**: File storage for level images and assets (via Spatie Media Library)

## Tech Stack

This project is built using the [Laravel Educational Starter Pack](https://github.com/ndeblauw/starterpack) and includes:

-   **Laravel 12**: PHP framework
-   **SQLite**: Lightweight database for development
-   **Laravel Breeze**: Authentication scaffolding
-   **Tailwind CSS**: Utility-first CSS framework (CDN)
-   **Alpine.js**: Lightweight JavaScript framework (CDN)
-   **Spatie Media Library**: Image and file management
-   **Pest PHP**: Testing framework
-   **Laravel Debugbar**: Development debugging tools

## Installation

### Prerequisites

-   PHP 8.3 or higher
-   Composer
-   Node.js and npm

### Setup

1. Clone the repository:

```bash
git clone <your-repo-url>
cd starterpack
```

2. Run the setup script:

```bash
composer setup
```

This will:

-   Install PHP dependencies
-   Copy `.env.example` to `.env`
-   Generate application key
-   Run database migrations
-   Install and build frontend assets

3. Start the development server:

```bash
composer dev
```

This runs multiple services concurrently:

-   Laravel development server (localhost:8000)
-   Queue worker
-   Log viewer (Pail)
-   Vite dev server

## Usage

### For Players

1. Register a new account
2. Create your player profile with a unique nickname
3. Browse available levels
4. Solve puzzles by writing command sequences
5. Earn XP and track your progress

### For Admins

1. Access the admin panel at `/admin`
2. Manage tiles (game elements)
3. Create and edit levels
4. View all player profiles

## Development

### Running Tests

```bash
composer test
```

### Code Quality

The project uses Laravel Pint for code formatting:

```bash
./vendor/bin/pint
```

### Database

View the current database schema:

```bash
php artisan db:show
```

Generate a new migration:

```bash
php artisan make:migration create_table_name
```

## Project Structure

```
app/
├── Http/Controllers/
│   ├── LevelController.php      # Admin level management
│   ├── PlayerController.php     # Admin player management
│   ├── TileController.php       # Admin tile management
│   └── Userzone/
│       ├── PlayerController.php # User player profile
│       └── ProfileController.php # User account settings
├── Models/
│   ├── User.php
│   ├── Player.php
│   ├── Level.php
│   ├── Tile.php
│   └── Score.php
database/
├── migrations/                   # Database migrations
└── seeders/                      # Database seeders
resources/
└── views/                        # Blade templates
routes/
├── web.php                       # Web routes
└── auth.php                      # Authentication routes
```

## Acknowledgments

-   Built with the [Laravel Educational Starter Pack](https://github.com/ndeblauw/starterpack)
-   Powered by [Laravel Framework](https://laravel.com)
