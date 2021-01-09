<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

## About this Project

this is a little bit convert version from <a href="https://github.com/mdipierro/evote">evote</a>

## How to Install
```
    1. composer install.
    2. cp .env.example .env (Root).
    3. Filling the env variable to your database.
    4. php artisan migrat:refresh. (Root).
    5. setting your web server to running this project (Nginx, LSWS, Apache, etc).
    6. daemon the queue with `php artisan queue:listen`.
    7. Run chmod command (777 or 775) to edit folder `bootstrap`, `storage`, `public`. (Root).
    8. Run `php artisan optimize:clear`. (Root).
```