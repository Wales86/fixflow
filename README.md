# FixFlow

FixFlow is a workshop management tool designed to accurately track the working time devoted to repair orders. The main goal of the application is to provide the data necessary for analyzing the profitability of repairs and the efficiency of the team, thus eliminating the need for paper documentation.

This system serves as the single source of truth for all activities related to recording working time in the workshop, enabling managers to make data-driven decisions to optimize operations.

[![License: ISC](https://img.shields.io/badge/License-ISC-blue.svg)](https://opensource.org/licenses/ISC)

## Table of Contents

- [Tech Stack](#tech-stack)
- [Project Scope](#project-scope)
- [Getting Started Locally](#getting-started-locally)
  - [Prerequisites](#prerequisites)
  - [With Docker (Recommended)](#with-docker-recommended)
  - [Manual Setup](#manual-setup)
- [Available Scripts](#available-scripts)
- [Project Status](#project-status)
- [License](#license)

## Tech Stack

The project is built using a modern monolithic architecture with the TALL stack philosophy, adapted for React.

-   **Backend**: [Laravel 12](https://laravel.com/) (PHP)
-   **Frontend**: [React 19](https://react.dev/) (TypeScript)
-   **Connector**: [Inertia.js](https://inertiajs.com/)
-   **Styling**: [Tailwind CSS 4](https://tailwindcss.com/)
-   **Database**: MySQL

## Project Scope

The Minimum Viable Product (MVP) version focuses on the core functionalities:

-   **Client Management**: CRUD operations for client profiles.
-   **Vehicle Management**: CRUD operations for vehicles, with the ability to assign them to clients and view their repair history.
-   **Order Management**: Create and manage repair orders, track their status (e.g., *New, In Progress, Awaiting Parts, Completed*), and associate them with clients and vehicles.
-   **Time Tracking**: A simple interface for mechanics to log time spent on specific tasks within a repair order.
-   **Reporting**: Basic reports on team efficiency and total time logged per order.

## Getting Started Locally

You can run the project using Laravel Sail (Docker) or by setting up a local PHP environment manually.

### Prerequisites

-   PHP >= 8.3
-   Composer
-   Node.js & NPM
-   [Docker](https://www.docker.com/products/docker-desktop/) (for Sail method)

### With Docker (Recommended)

This is the simplest way to get up and running.

1.  **Clone the repository**
    ```bash
    git clone https://github.com/your-username/fixflow.git
    cd fixflow
    ```

2.  **Copy Environment File**
    ```bash
    cp .env.example .env
    ```
    *No need to modify database credentials; Sail handles them automatically.*

3.  **Install Dependencies**
    ```bash
    docker run --rm \
        -u "$(id -u):$(id -g)" \
        -v "$(pwd):/var/www/html" \
        -w /var/www/html \
        laravelsail/php84-composer:latest \
        composer install --ignore-platform-reqs
    ```

4.  **Start Sail Containers**
    ```bash
    ./vendor/bin/sail up -d
    ```

5.  **Install NPM Dependencies**
    ```bash
    ./vendor/bin/sail npm install
    ```

6.  **Generate Application Key**
    ```bash
    ./vendor/bin/sail artisan key:generate
    ```

7.  **Run Database Migrations**
    ```bash
    ./vendor/bin/sail artisan migrate
    ```

8.  **Run Dev Server**
    ```bash
    ./vendor/bin/sail npm run dev
    ```

The application will be available at [http://localhost](http://localhost).

### Manual Setup

1.  **Clone the repository**
    ```bash
    git clone https://github.com/your-username/fixflow.git
    cd fixflow
    ```

2.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

3.  **Install JS Dependencies**
    ```bash
    npm install
    ```

4.  **Set up Environment File**
    ```bash
    cp .env.example .env
    ```

5.  **Configure `.env`**
    Update the `DB_*` variables with your local database credentials.

6.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

7.  **Run Database Migrations**
    ```bash
    php artisan migrate
    ```

8.  **Run the Development Servers**
    In one terminal, run the Vite server:
    ```bash
    npm run dev
    ```
    In another terminal, run the PHP server:
    ```bash
    php artisan serve
    ```

The application will be available at [http://localhost:8000](http://localhost:8000).

## Available Scripts

You can run the following scripts from the project root:

-   `npm run dev`: Starts the Vite development server with hot-reloading.
-   `npm run build`: Compiles and bundles the assets for production.
-   `npm run lint`: Lints the TypeScript/React files.
-   `npm run format`: Formats code using Prettier.

For Laravel commands, use `php artisan` or `./vendor/bin/sail artisan` if using Docker.

## Project Status

This project is currently in the **MVP development phase**. Core features are being actively built.

## License

This project is licensed under the ISC License. See the LICENSE file for details.
