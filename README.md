# Chat with Laravel Reverb

## Overview
A simple chat
[Laravel Reverb Documentation](https://laravel.com/docs/11.x/reverb)
[Pusher Channels Documentation](https://laravel.com/docs/11.x/broadcasting#client-pusher-channels)
[ShouldBroadcast Interface Documentation](https://laravel.com/docs/11.x/broadcasting#the-shouldbroadcast-interface)
[Listening for Event Broadcasts Documentation] (https://laravel.com/docs/11.x/broadcasting#listening-for-event-broadcasts)


## Tech Stack
Laravel Version 11.2.0

## Getting Started

### Prerequisites
- Docker
- PHP >= 8.2
- Composer
- Node.js and npm

### Installation Guide
Follow these steps to set up the "laravel-reverb-chat" project on your local machine:

#### 1. Clone the project
Open a terminal in your desired directory and clone the repository:
```bash
  git clone https://github.com/bertogross/laravel-reverb-chat.git
```
```bash
  cd laravel-reverb-chat
```

#### 2. Install Project Dependencies
Navigate to the project's root directory and install the required PHP and JavaScript dependencies:
```bash
  npm install
```
```bash
  composer install
```

#### 3. Start Docker Containers (in a new terminal)
Ensure Docker is installed and running on your system. Then, initialize the Docker containers:
```bash
  docker-compose up --build
```

#### 4. Set Up the Database
Start your web server and set up the database schema with Laravel's migration feature:
```bash 
  php artisan migrate 
```

#### 5. Compile Frontend Assets (in a new terminal)
Compile the frontend assets using Laravel Mix:
```bash 
  npm run dev
```

#### 6. Start the server (in a new terminal)
Start the Laravel development server:
```bash 
  php artisan serve
```


#### 6. Start the Reverb server (in a new terminal)
```bash 
  php artisan reverb:start --host=127.0.0.1 --port=9000
```

## Accessing the Application
#### Application: http://localhost:8000
#### phpMyAdmin: http://localhost:8080

## License
This README.md provides a structured and detailed guide for setting up and running the simple Chat Laravel Reverb project. It includes prerequisites, a step-by-step installation guide, and links for accessing the application and its documentation.
