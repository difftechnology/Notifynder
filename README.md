Notifynder 3.2 - Laravel 5
==========================

Notifynder is designed to manage notifications in a powerful and easy way.
With the flexibility that Notifynder offer, It provide a complete API to work with your notifications,
such as storing, retriving, and organise your codebase to handle hundreds of notifications.
You get started in a couple of minutes to "enable" notifications in your Laravel Project.

Compatible DBs: **MySql** - **PostgresSql** - **Sqlite**

Documentation: **[Notifynder Wiki](https://github.com/difftechnology/Notifynder/wiki)**

- - -

## Installation ##

### Step 1 ###

Add it on your `composer.json`

    "difftechnology/notifynder": "^3.2"

and run 

    composer update

or run

    composer require difftechnology/notifynder


### Step 2 ###

Add the following string to `config/app.php`

**Providers array:**

    difftechnology\Notifynder\NotifynderServiceProvider::class,

**Aliases array:**

    'Notifynder' => difftechnology\Notifynder\Facades\Notifynder::class,


### Step 3 ###

#### Migration ####

Publish the migration as well as the configuration of notifynder with the following command:

    php artisan vendor:publish --provider="difftechnology\Notifynder\NotifynderServiceProvider"

Run the migration

    php artisan migrate

### Quick Usage ###

Set up category of notification, think about it as the
body of the notification:

    php artisan notifynder:create:category "user.following" "{from.username} started to follow you"

To send a notification with notifynder, that's all
you have to do.

~~~php
Notifynder::category('user.following')
            ->from($from_user_id)
            ->to($to_user_id)
            ->url('http://www.yourwebsite.com/page')
            ->send();
~~~

**Retrieving Notifications**

~~~php
// @return Collection
Notifynder::getAll($user_id,$limit,$paginateBool);
~~~

**Reading Notifications:**
~~~php
// @return number of notifications read
Notifynder::readAll($user_id);
~~~

To know more, such as the advance usage of Notifynder Visit the **[Notifynder Wiki](https://github.com/fenos/Notifynder/wiki)**.

#### Forked From ####

Fenos/Notifynder
