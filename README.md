# BookingSystem

A booking system for businesses and clients.

## Quick Start

1. Clone and setup:

```bash
    git clone https://github.com/zsmartin03/booking-system.git
    cd booking-system
    composer install
    npm install
```

2. Configure environment:

```bash
cp .env.example .env
```

3. Run migrations & seed:

```bash
php artisan migrate --seed
```

4. Build and run:

```bash
npm run dev
```

This will start both Vite and Laravel's PHP server simultaneously using `concurrently`.
