# LaravelReverb
Chat with Laravel Reverb
[laravel.com/docs/11.x/reverb](https://laravel.com/docs/11.x/reverb)


## Tech Stack
Laravel Version 11.2.0


## Installation
To get started with the project, follow the steps below:

1) Clone the project
##### In your working directory, open the terminal of your IDE and run the following command:
```bash
  git clone https://github.com/bertogross/LaravelReverb.git
```
```bash
  cd LaravelReverb
```

2) Run Docker
##### Make sure you have Docker installed on your system. In your IDE, open the terminal and execute the following command: 
```bash
  docker-compose up --build
```

3) Install dependencies
##### Navigate to the root project directory "LaravelReverb" and execute the following commands:
```bash
  npm install
```
```bash
  composer install
```

4) Deploy the database
##### Ensure that a web server is running, and execute the following command:
```bash 
  docker-compose exec laravel.reverb php artisan migrate 
```

5) Compile assets
##### Compile the assets by running the command:
```bash 
  npm run dev
```

6) Start the server
##### Launch the server with the following command:
```bash 
  php artisan serve
```

Go to: http://localhost:8000/
phpMyAdmin are in: http://localhost:8080
