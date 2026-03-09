# Spaceship

Symfony 7 application powered by FrankenPHP with RabbitMQ messaging.

## Requirements

- Docker & Docker Compose

## Getting Started

```bash
# Clone and start
git clone <repo-url> && cd spaceship
docker compose up --build -d
```

The app will be available at:
- **HTTP:** http://localhost:8080
- **HTTPS:** https://localhost

RabbitMQ Management UI: http://localhost:15672 (guest/guest)

## Architecture

| Service    | Image                              | Port(s)          |
|------------|------------------------------------|------------------|
| php        | FrankenPHP (Caddy + PHP worker)    | 8080 (HTTP), 443 (HTTPS) |
| rabbitmq   | RabbitMQ 3.13 Management Alpine    | 5672, 15672      |

FrankenPHP replaces the traditional PHP-FPM + Nginx stack with a single container that embeds PHP directly into the Caddy web server, using worker mode for better performance.

## Environment Variables

Configure via `.env` file or shell environment:

| Variable                   | Default                  |
|----------------------------|--------------------------|
| `APP_ENV`                  | `dev`                    |
| `APP_SECRET`               | `change_me_in_production`|
| `SERVER_NAME`              | `localhost`              |
| `RABBITMQ_USER`            | `guest`                  |
| `RABBITMQ_PASS`            | `guest`                  |
| `RABBITMQ_VHOST`           | `/`                      |

## Common Commands

```bash
# Start services
docker compose up -d

# Rebuild after Dockerfile changes
docker compose up --build -d

# View logs
docker compose logs -f php

# Stop services
docker compose down

# Run Symfony console commands
docker compose exec php php bin/console <command>

# Install/update Composer dependencies
docker compose exec php composer install
```
