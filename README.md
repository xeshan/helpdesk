

# Helpdesk Project

## A Laravel-based helpdesk application with AI-powered features.

### Installation

Step 1: Clone the Repository
```bash

git clone https://github.com/xeshan/helpdesk.git

cd helpdesk
```

Step 2: Install Composer Dependencies

```bash
composer install
```
Step 3: Configure Environment Variables

1. Copy the .env.example file to .env and update the following variables:
```bash
cp .env.example .env
```
2. Database settings (e.g., DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD)

OpenAI settings:

1. OPENAI_CLASSIFY_ENABLED: Set to true to enable AI features or false to disable them.
2. OPENAI_API_KEY: Enter your real OpenAI API key if OPENAI_CLASSIFY_ENABLED is set to true.
3. Queue connection: Set QUEUE_CONNECTION to database for realistic behavior or sync for local development.

Step 4: Generate Application Key and Migrate Database

```bash
php artisan key:generate
php artisan migrate
```
Step 5: Install Frontend Dependencies and Build Assets

```bash
npm install
npm run dev  # For development environment
npm run build  # For production environment
```


### To add dummy data 

```bash
php artisan db:seed TicketSeeder
```

### To classify bulk data using command line

```bash
php artisan tickets:bulk-classify
```