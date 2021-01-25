
# <p align="center">Emotional API</p>
# <p align="center">![Emotional API](logo.png)</p>

--------

# About Emotional API

Emotional API is a web REST-Ful API based on Laravel framework, which serve as services and data provider for **[Emotional Frontend](https://github.com/limitless-brain/emotional-react)**.


# Getting Started
Emotional API using **[Spotify](https://developer.spotify.com/)** and **[Youtube](https://developers.google.com/youtube/v3)** APIs to provide songs data. You must have APIs keys and secrets in order to have fully functional APIs.

## Manual Setup
You need to install the **[required libraries](#required-libraries)** and configure the **[Environment Variables](#environment-variables)** in .env file with your APIs keys and secrets.

### Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/8.x/installation)

Alternative installation is possible without local dependencies relying on [Docker](#docker).

Clone the repository

    git clone git@github.com:limitless-brain/emotional-backend.git

Switch to the repo folder

    cd emotional-backend

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Configure passport

    php artisan passport:install

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Start the local development server

    php artisan serve

You can now access the server at http://localhost:8000

**TL;DR command list**

    git clone git@github.com:limitless-brain/emotional-backend.git
    cd emotional-backend
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan passport:install

**Make sure you set the correct database connection information before running the migrations** [Environment variables](#environment-variables)

    php artisan migrate
    php artisan serve

### Required Files
Due to github file size restrictions, big files has been moved to google drive.
[**Shared Files**](https://drive.google.com/drive/folders/1v9ygEWii37KPXUdZ2EhkL3PxUvmsKbuI?usp=sharing)
* Emotional AI files should be extracted to /app/Python .
* Emotional db files should be extracted to /database .

### Database seeding

**Populate the database with seed data with relationships which includes users, articles, comments, tags, favorites and follows. This can help you to quickly start testing the api or couple a frontend and start using it with ready content.**

Open the DummyDataSeeder and set the property values as per your requirement

    database/seeders/DatabaseSeeder.php

Run the database seeder, and you're done

    php artisan db:seed

***Note*** : It's recommended to have a clean database before seeding. You can refresh your migrations at any point to clean the database by running the following command

    php artisan migrate:refresh

### Required Libraries
- [Python3](https://www.python.org/) to call the AI model and
  make the prediction.
  > sudo apt install python3 python3-pip
    - In order to install the required libraries for the AI and test at the same time,
      run the script model script.
      > python3 app/Python/emotional_ai.py


- [YT-DL](https://github.com/ytdl-org/youtube-dl) to download videos from youtube.
  ```
  sudo curl -L https://yt-dl.org/downloads/latest/youtube-dl -o /usr/local/bin/youtube-dl
  sudo chmod a+rx /usr/local/bin/youtube-dl
  sudo apt install ffmpeg
  ```

### Docker

To install with [Docker](https://www.docker.com), run following commands:

```
git clone git@github.com:gothinkster/laravel-realworld-example-app.git
cd laravel-realworld-example-app
cp .env.example.docker .env
docker run -v $(pwd):/app composer install
cd ./docker
docker-compose up -d
docker-compose exec php php artisan key:generate
docker-compose exec php php artisan jwt:generate
docker-compose exec php php artisan migrate
docker-compose exec php php artisan db:seed
docker-compose exec php php artisan serve --host=0.0.0.0
```

The api can be accessed at [http://localhost:8000/api](http://localhost:8000/api).

## API Specification

This contains all APIs specification which make the development of frontend much easier.

> [Full API Spec](/api)

----------

# Code overview

## Dependencies

- [passport](https://github.com/tymondesigns/jwt-auth) - For authentication using JSON Web Tokens
- [laravel-cors](https://github.com/barryvdh/laravel-cors) - For handling Cross-Origin Resource Sharing (CORS)

## Folders

- `app/Models` - Contains all the Eloquent models
- `app/Http/Controllers/Api` - Contains all the api controllers
- `app/Http/Middleware` - Contains the Passport auth middleware
- `config` - Contains all the application configuration files
- `database/factories` - Contains the model factory for all the models
- `database/migrations` - Contains all the database migrations
- `database/seeds` - Contains the database seeder
- `routes` - Contains all the api routes defined in api.php file
- `tests` - Contains all the application tests

## Environment variables

- `.env` - Environment variables can be set in this file
    - ### Database
      >- DB_CONNECTION=mysql
      >- DB_HOST=127.0.0.1
      >- DB_PORT=3306
      >- DB_DATABASE=emotional
      >- DB_USERNAME=fswt
      >- DB_PASSWORD=FSwt.1994
    - ### Spotify
      >- SPOTIFY_CLIENT_ID="YOUR CLIENT ID"
      >- SPOTIFY_CLIENT_SECRET="YOUR CLIENT SECRET"
    - ### Youtube
      >- YOUTUBE_API_SECRET_KEY="YOUR SECRET"

----------

# Testing API

Run the laravel development server

    php artisan serve

The api can now be accessed at

    http://localhost:8000/api/v1

Request headers

| **Required** 	| **Key**              	| **Value**            	|
|----------	|------------------	|------------------	|
| Yes      	| Content-Type     	| application/json 	|
| Yes      	| X-Requested-With 	| XMLHttpRequest   	|
| Optional 	| Authorization    	| Token {Bearer}      	|

Refer the [api specification](#api-specification) for more info.

----------

# Authentication

This application uses Passport Tokens to handle authentication. The token is passed with each request using the `Authorization` header with `Token` scheme. The Passport authentication middleware handles the validation and authentication of the token. Please check the following source to learn more about Passport.

- **[Laravel Passport](https://laravel.com/docs/8.x/passport)**

----------

# Cross-Origin Resource Sharing (CORS)

This applications has CORS enabled by default on all API endpoints. The default configuration allows requests from `http://localhost:3000` to help speed up your frontend testing. The CORS allowed origins can be changed by setting them in the config file. Please check the following sources to learn more about CORS.

- [Mozilla Access Control CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS)
- [Wikipedia CORS](https://en.wikipedia.org/wiki/Cross-origin_resource_sharing)
- [W3 CORS](https://www.w3.org/TR/cors)
