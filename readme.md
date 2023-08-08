```markdown
# Parking Search Service

The Parking Search Service is a Laravel application that provides an API for searching and ranking parking spaces and park-and-ride locations based on user coordinates.

## Features

- Search for nearby parking spaces and park-and-ride locations.
- Rank search results using external services.
- Handle slow responses from external services.
- Return search results in a specific JSON format.

## Getting Started

### Prerequisites

- PHP 7.4+
- Composer
- MySQL or other compatible database
- Docker (optional, for local development with Docker)

### Installation

1. Clone the repository:

   ```shell
   git clone https://github.com/OlabodeAbesin/search-service.git
   cd parking-search-service
   ```

2. Install dependencies:

   ```shell
   composer install
   ```

3. Copy the `.env.example` file to `.env`:

   ```shell
   cp .env.example .env
   ```

4. Update the `.env` file with your database credentials and other settings.

5. Generate the application key:

   ```shell
   php artisan key:generate
   ```

6. Run database migrations and seeders:

   ```shell
   php artisan migrate --seed
   ```

### Usage

1. Start the development server:

   ```shell
   php artisan serve
   ```

2. Access the API at `http://127.0.0.1:8000/api/search` using your preferred API client (e.g., Postman).

### Testing

Run the unit tests:

```shell
php artisan test
```
