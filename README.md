# AddChat Laravel Lite

Welcome to AddChat Laravel Lite.

### All-in-one multi-purpose Chat Widget Laravel Pacakge

AddChat is a new chatting friend of Laravel. It's a standalone Chat widget that uses the website's existing `users` base, and let website users chat with each other. 

<br>

You get full source-code, hence AddChat lives and runs on your server/hosting including database. And therefore, you get complete privacy over your data. Either you're a big corporate sector or a small business. AddChat is for everyone.

---

#### Read the documentation live - [AddChat Laravel Lite Docs](https://addchat-docs.classiebit.com)

#### Live Preview - [AddChat Laravel Lite](https://addchat-laravel.classiebit.com)

---

![AddChat Lite - Laravel Chat Widget](https://addchat-docs.classiebit.com/images/addchat-docs-banner-1.jpg "AddChat Lite - Laravel Chat Widget")

---

> **Here's a complete video tutorial guide for getting started quickly [AddChat Laravel Academy](https://classiebit.com/academy/addchat-laravel/getting-started) ✌️**

---

## Overview

**Addchat Lite** is a chat widget that you can integrate into an existing or a fresh Laravel website. AddChat works like a standalone widget and fulfills all your business-related needs like -

1. User-to-user chatting
2. Live real-time chatting (without page refresh)
3. **Internal** notification system (saves **Pusher** monthly subscription fees)
4. Customer support (<larecipe-badge type="black" circle icon="fa fa-lock"></larecipe-badge> Pro)
5. Multi-user groups (<larecipe-badge type="black" circle icon="fa fa-lock"></larecipe-badge> Pro)

and a lot more features available in **AddChat Pro** ⚡️


## Why AddChat ?

Some of the key highlights, why you would like to go with AddChat!

- Save monthly subscription bills (pay once use forever)
- No Confidential Data leak
- Complete Privacy
- Easy to install & update
- Use existing users database
- Multi-purpose, use it as Helpdesk, Customer support, User-to-user chatting and much more...

---

> **AddChat never modifies your existing database tables or records. And it never breaks down any of your website functionality.**

---

> **AddChat is fully tested and ready to be used in production websites.**

---


## Technical Specification

AddChat is very light, high performance, scalable and secure.

1. AddChat front-end built with **VueJs**, which is purely API based web-app.

2. AddChat back-end (API) built with **Laravel**

    - **AddChat Laravel** version is a Laravel package, hence, can be installed via **Composer** in an existing or a fresh Laravel website.



## User Interface & Design

AddChat is designed in **CSS Flexbox** and **Sass**. Let's see what's so special about **CSS Flexbox** and why we used it.

1. AddChat is a CSS Framework Independent. Means, no matter in which CSS Framework your website is in, it neither affects the website CSS nor gets affected by it.

    - [Bootstrap](https://getbootstrap.com/) 
    - [Bulma](https://bulma.io/) 
    - [Materializecss](https://materializecss.com/) 
    - [Semantic UI](https://semantic-ui.com/) 
    - [UIKit](https://getuikit.com/) 
    - [Zurb Foundation](https://foundation.zurb.com/) 

    or any other...

2. AddChat CSS is completely encapsulated (wrapped in AddChat wrapper with `#addchat-bot .c-` prefix).
    - Hence, it never override your website CSS nor inherits from it.

    - AddChat UI is extra-responsive. Optimized for **extra-small** devices to large **4K desktops** -

        * Small phones
        * Android Phones
        * iPhones
        * iPad & iPad Pro
        * Small-Medium Size Laptops
        * Large Desktops

3. We've used the popular **NPM** package `auto-prefixer` to make the AddChat UI design same across all types of browsers e.g `Chrome, Firefox, Safari, Edge` etc



## Multi-regional

AddChat is compatible with all languages and timezones. AddChat auto adapts and adjust regional settings according to your website's default timezone and language. Please refer to the Language section for more info about **adding a new language** in [AddChat Laravel](https://addchat-docs.classiebit.com/docs/1.0/admin/multi-language-laravel)

--- 

> **AddChat never breaks any of your website functionality, even if something went wrong with AddChat, there are `fallback modes` for every worst-case scenario.**

---


## Lite Version

---

> **This is AddChat Lite version documentation**

---


**AddChat Lite** is open-source, free to use. Lite version has got limited features & functionality.

- **AddChat Laravel Lite**

    + [Live](https://addchat-laravel.classiebit.com) - Visit live.
    + [Github](https://github.com/classiebit/addchat-laravel) - Give us a Star.
    + [Install](https://classiebit.com/addchat-laravel) - Visit here to install


## Pro Version

**AddChat Pro Version** comes with **Commercial** license. Pro version is fully loaded with a lot of useful and exciting features.

- **AddChat Laravel Pro**

    + [Live](https://addchat-laravel-pro.classiebit.com) - Live preview available now.
    + [Purchase](https://classiebit.com/addchat-laravel-pro) - Available for purchase now - Flat 50% Off (limited time offer)


---

# Laravel Installation

AddChat can be installed via composer. Smooth... 🍻

---

> **Here's a complete video tutorial guide for getting started quickly [AddChat Laravel Academy](https://classiebit.com/academy/addchat-laravel/getting-started) ✌️**

---


### Prerequisites

* Laravel version 5.5 / 5.6 / 5.7 / 5.8 / 6.x / 7.x / 8.x
* Make sure to install AddChat package on a **Fresh** or **Existing** Laravel application. 
* We also assume that you've setup the database.
* If you're running MySql version older than < 5.7 then disable strict mode in Laravel `config/database.php` `'strict' => false`


### Install

1. If installing AddChat on an existing Laravel application and you already have **Auth** system then **skip this step**

    If installing on a **Fresh Laravel application** then run 

    **For Laravel 5.5 to 5.8**

    ```php
    php artisan make:auth

    php artisan migrate
    ```

    **For Laravel 6.x**

    ```php
    composer require laravel/ui --dev

    php artisan ui vue --auth

    npm install && npm run dev

    php artisan migrate
    ```

2. Install AddChat via Composer

    ```php
    composer require classiebit/addchat-laravel
    ```

3. Run AddChat install command

    ```php
    php artisan addchat:install
    ```

4. Open the common layout file, mostly the common layout file is the file which contains the HTML & BODY tags.

    - Copy AddChat CSS code and paste it right before closing **&lt;/head&gt;** tag

        ```php
        <!-- 1. Addchat css -->
        <link href="<?php echo asset('assets/addchat/css/addchat.min.css') ?>" rel="stylesheet">
        ```
    
    - Copy AddChat Widget code and paste it right after opening **&lt;body&gt;** tag

        ```php
        <!-- 2. AddChat widget -->
        <div id="addchat_app" 
            data-baseurl="<?php echo url('') ?>"
            data-csrfname="<?php echo 'X-CSRF-Token' ?>"
            data-csrftoken="<?php echo csrf_token() ?>"
        ></div>
        ```

    - Copy AddChat JS code and paste it right before closing **&lt;/body&gt;** tag

        ```php
        <!-- 3. AddChat JS -->
        <!-- Modern browsers -->
        <script type="module" src="<?php echo asset('assets/addchat/js/addchat.min.js') ?>"></script>
        <!-- Fallback support for Older browsers -->
        <script nomodule src="<?php echo asset('assets/addchat/js/addchat-legacy.min.js') ?>"></script>
        ```

    >{warning} Please replace **&lt;php ?>** tag by **{{}}** curly brackets.


    #### The final layout will look something like this

    ```php
    <head>

        <!-- **** your site other content **** -->

        <!-- 1. Addchat css -->
        <link href="<?php echo asset('assets/addchat/css/addchat.min.css') ?>" rel="stylesheet">

    </head>
    <body>

        <!-- 2. AddChat widget -->
        <div id="addchat_app" 
            data-baseurl="<?php echo url('') ?>"
            data-csrfname="<?php echo 'X-CSRF-Token' ?>"
            data-csrftoken="<?php echo csrf_token() ?>"
        ></div>

        
        <!-- **** your site other content **** -->


        <!-- 3. AddChat JS -->
        <!-- Modern browsers -->
        <script type="module" src="<?php echo asset('assets/addchat/js/addchat.min.js') ?>"></script>
        <!-- Fallback support for Older browsers -->
        <script nomodule src="<?php echo asset('assets/addchat/js/addchat-legacy.min.js') ?>"></script>

    </body>
    ```

---

> **For Info, the `php artisan addchat:install` publishes AddChat assets to your application `public/assets` directory**

---

> **`addchat.min.js` for modern browsers & `addchat-legacy.min.js` for older browsers. These will be used switched by the browsers automatically on the basis on `type="module"` & `nomodule`, you need to nothing.**

---

> **Setup finishes here, now heads-up straight to [Settings](https://addchat-docs.classiebit.com/docs/1.0/admin/settings) docs**

---

#### Must read the documentation for getting started - [AddChat Laravel Lite Docs](https://addchat-docs.classiebit.com)

---