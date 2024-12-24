# Laravel Project with TailwindCSS

This README provides an overview and setup instructions for a Laravel project that uses TailwindCSS for styling.

## Prerequisites

Before getting started, ensure you have the following installed on your system:

- [PHP](https://www.php.net/) (>= 8.2 recommended)
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) with npm or [Yarn](https://yarnpkg.com/)
- [Laravel CLI](https://laravel.com/docs/installation)

## Installation

1. **Clone the Repository:**

   ```bash
   git clone https://github.com/kmow-heal/test-task-car-booking
   cd test-task-car-booking
   ```

2. **Install PHP Dependencies:**

   ```bash
   composer install
   ```

3. **Install JavaScript Dependencies:**

   ```bash
   npm install
   ```

4. **Set Up Environment:**

   Copy the `.env.example` file to `.env` and configure your environment settings:

   ```bash
   cp .env.example .env
   ```

   Generate the application key:

   ```bash
   php artisan key:generate
   ```

5. **Run Database Migrations:**

   Set up your database configuration in the `.env` file and run migrations:

   ```bash
   php artisan migrate
   ```

6. **Compile Assets:**

   Compile the TailwindCSS and other assets:

   ```bash
   npm run dev
   # or for production
   npm run prod
   ```

7. **Run the Development Server:**

   Start the Laravel development server:

   ```bash
   php artisan serve
   ```

   The application will be available at [http://localhost:8000](http://localhost:8000).

## Run with Docker

1. **Run docker-compose with mysql, phpmyadmin:**
    Run containers
   ```bash
   docker-compose up -d
   ```
   Stop containers 
    ```bash
   docker-compose stop
    ```
1. **Run docker container:**
    Run container

   ```bash
   docker run -d --build-arg user=dev uid=1000 .
   ```
   
   Stop container

   ```bash
   docker stop <container_name_or_id>
   ```

## TailwindCSS Integration

This project uses TailwindCSS for styling. The configuration can be found in the `tailwind.config.js` file. To customize or extend styles, edit this file and recompile the assets using:

```bash
npm run dev
```

### Adding New Styles

1. Edit the `tailwind.config.js` file to add customizations.
2. Add styles directly in your Blade or Vue components using Tailwind classes.
3. Compile assets to apply changes.

## Deployment

For production, make sure to:

1. Set `APP_ENV` to `production` and configure the `.env` file accordingly.

2. Run the following commands:

   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   npm run prod
   ```

3. Use a web server like Nginx or Apache to serve the application.

## Contribution Guidelines

If you'd like to contribute:

1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Submit a pull request with a detailed description of your changes.

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Acknowledgements

- [Laravel](https://laravel.com/)
- [TailwindCSS](https://tailwindcss.com/)
- [Laravel Community](https://laracasts.com/discuss)

