# Circuits API

Backend API for **Circuits**, a grid-based programming puzzle game designed to teach programming logic through interactive challenges.

## About the Project

This is the **API and administration backend** for Circuits, an educational game where players control a robot navigating through circuit boards. The API handles all game logic, level management, player profiles, and scoring.

### Key Features

- **RESTful API**: Clean endpoint design for game client integration, authenticated via Bearer tokens
- **Player Management**: Registration, authentication, player profiles with nickname and XP tracking
- **Level System**: Browse, filter, and complete puzzle levels with configurable grids and difficulty settings
- **Scoring & Progression**: XP calculation based on efficiency, first-completion bonuses, and progress tracking
- **Level Validation**: Real-time validation of level configurations and tile reachability for the level editor
- **Trivia**: Random programming trivia questions fetched from Open Trivia DB
- **Admin Panel**: Web-based administration interface for managing tiles, levels, and players
- **API Documentation**: Auto-generated interactive docs powered by Scribe and Scalar UI

### Database Schema

The API uses a relational database with the following main entities:

- **Users** — Authentication and account management (with admin capabilities)
- **Players** — Game profiles (one-to-one with users) tracking nickname and XP
- **Levels** — Puzzle configurations with grid layout, difficulty, and constraints
- **Tiles** — Building blocks for levels (empty, circuit, obstacle types)
- **Scores** — Player achievements on completed levels
- **Media** — File storage for level images and tile assets (via Spatie Media Library)

## Tech Stack

Built using the [Laravel Educational Starter Pack](https://github.com/ndeblauw/starterpack).

| Category | Technology |
|---|---|
| Framework | Laravel 12 |
| PHP | 8.3+ |
| Authentication | Laravel Sanctum (Bearer tokens) |
| Database | SQLite |
| Auth Scaffolding | Laravel Breeze |
| Frontend | Tailwind CSS, Alpine.js, Livewire 4 |
| Media Management | Spatie Media Library |
| API Documentation | Scribe 5 + Scalar UI |
| Email | Resend |
| Backups | Spatie Laravel Backup |
| Testing | Pest PHP |
| Code Style | Laravel Pint |
| Debugging | Laravel Debugbar |

## Installation

### Prerequisites

- PHP 8.3 or higher
- Composer
- Node.js and npm

### Setup

1. Clone the repository:

```bash
git clone https://github.com/ElizabethCF01/circuits-web-game.git
cd circuits-web-game
```

2. Run the setup script:

```bash
composer setup
```

This will install PHP and Node dependencies, copy `.env.example` to `.env`, generate the application key, run migrations, and build frontend assets.

3. Start the development server:

```bash
composer dev
```

This runs multiple services concurrently:

- Laravel development server (`localhost:8000`)
- Queue worker
- Log viewer (Pail)
- Vite dev server

## API Endpoints

All API routes are prefixed with `/api`. Authentication is handled via Bearer tokens using Laravel Sanctum.

### Authentication

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/api/auth/register` | No | Register a new user |
| `POST` | `/api/auth/login` | No | Login and receive a token |
| `POST` | `/api/auth/logout` | Yes | Revoke current token |
| `GET` | `/api/auth/user` | Yes | Get authenticated user |
| `POST` | `/api/auth/forgot-password` | No | Send password reset email |
| `POST` | `/api/auth/reset-password` | No | Reset password with token |

### Player

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/api/player` | Yes | Get player profile |
| `POST` | `/api/player` | Yes | Create player profile |
| `PUT` | `/api/player` | Yes | Update player profile |
| `DELETE` | `/api/player` | Yes | Delete player profile |
| `GET` | `/api/player/progress` | Yes | Get progress with scores |

### Levels

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/api/levels` | No | List levels (paginated, filterable) |
| `GET` | `/api/levels/{level}` | No | Get level details |
| `POST` | `/api/levels/{level}/complete` | Yes | Submit level solution |

### Level Validation

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `POST` | `/api/levels/validate` | No | Validate level configuration |
| `POST` | `/api/levels/reachability` | No | Check tile reachability |

### Tiles

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/api/tiles` | No | List all tile types |

### Trivia

| Method | Endpoint | Auth | Description |
|---|---|---|---|
| `GET` | `/api/trivia` | No | Get a random trivia question |

## API Documentation

Interactive API documentation is generated with [Scribe](https://scribe.knuckles.wtf/) and served via the [Scalar](https://scalar.com/) UI.

### Accessing the Docs

Once the server is running, visit:

```
http://localhost:8000/docs
```

The documentation includes an interactive **Try It Out** feature, request/response examples, and a downloadable OpenAPI 3.0 specification.

### Regenerating the Docs

After making changes to controllers or docblock annotations, regenerate the documentation:

```bash
php artisan scribe:generate
```

### Exported Formats

- **OpenAPI spec**: `public/docs/openapi.yaml`
- **Postman collection**: `public/docs/collection.json`

## Project Structure

```
app/
├── Enums/                         # Game enums (difficulty levels)
├── Http/
│   ├── Controllers/
│   │   ├── Api/                   # REST API controllers
│   │   │   ├── AuthController.php
│   │   │   ├── LevelController.php
│   │   │   ├── LevelValidationController.php
│   │   │   ├── PlayerController.php
│   │   │   ├── TileController.php
│   │   │   └── TriviaController.php
│   │   ├── Auth/                  # Web authentication controllers
│   │   └── Userzone/              # Web user-facing controllers
│   └── Requests/                  # Form request validation
├── Models/
│   ├── User.php
│   ├── Player.php
│   ├── Level.php
│   ├── Tile.php
│   └── Score.php
├── Services/                      # Business logic services
├── Listeners/                     # Event listeners
├── Livewire/                      # Livewire components
└── Mail/                          # Mail templates
config/
├── scribe.php                     # API documentation config
database/
├── migrations/
└── seeders/
routes/
├── api.php                        # API routes
├── web.php                        # Web routes
└── auth.php                       # Authentication routes
public/
└── docs/                          # Generated API documentation
```

## Development

### Running Tests

```bash
composer test
```

### Code Style

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

## Acknowledgments

- Built with the [Laravel Educational Starter Pack](https://github.com/ndeblauw/starterpack)
- Powered by [Laravel](https://laravel.com)
- API documentation by [Scribe](https://scribe.knuckles.wtf/) and [Scalar](https://scalar.com/)
