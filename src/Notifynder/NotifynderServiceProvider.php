<?php namespace Difftechnology\Notifynder;

use Difftechnology\Notifynder\Artisan\CreateCategory;
use Difftechnology\Notifynder\Artisan\DeleteCategory;
use Difftechnology\Notifynder\Artisan\CreateGroup;
use Difftechnology\Notifynder\Artisan\PushCategoryToGroup;
use Difftechnology\Notifynder\Builder\NotifynderBuilder;
use Difftechnology\Notifynder\Categories\CategoryRepository;
use Difftechnology\Notifynder\Categories\CategoryManager;
use Difftechnology\Notifynder\Contracts\CategoryDB;
use Difftechnology\Notifynder\Contracts\NotificationDB;
use Difftechnology\Notifynder\Contracts\NotifynderCategory;
use Difftechnology\Notifynder\Contracts\NotifynderDispatcher;
use Difftechnology\Notifynder\Contracts\NotifynderGroup;
use Difftechnology\Notifynder\Contracts\NotifynderGroupCategoryDB;
use Difftechnology\Notifynder\Contracts\NotifynderGroupDB;
use Difftechnology\Notifynder\Contracts\NotifynderNotification;
use Difftechnology\Notifynder\Contracts\NotifynderSender;
use Difftechnology\Notifynder\Contracts\NotifynderTranslator;
use Difftechnology\Notifynder\Contracts\StoreNotification;
use Difftechnology\Notifynder\Groups\GroupManager;
use Difftechnology\Notifynder\Groups\GroupCategoryRepository;
use Difftechnology\Notifynder\Groups\GroupRepository;
use Difftechnology\Notifynder\Handler\Dispatcher;
use Difftechnology\Notifynder\Models\NotificationCategory;
use Difftechnology\Notifynder\Models\NotificationGroup;
use Difftechnology\Notifynder\Notifications\NotificationManager;
use Difftechnology\Notifynder\Notifications\NotificationRepository;
use Difftechnology\Notifynder\Parsers\ArtisanOptionsParser;
use Difftechnology\Notifynder\Parsers\NotifynderParser;
use Difftechnology\Notifynder\Senders\SenderFactory;
use Difftechnology\Notifynder\Senders\SenderManager;
use Difftechnology\Notifynder\Translator\Compiler;
use Difftechnology\Notifynder\Translator\TranslatorManager;
use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider;

class NotifynderServiceProvider extends ServiceProvider
{

    /**
     * Register Bindings
     */
    public function register()
    {
        $this->notifynder();
        $this->senders();
        $this->notifications();
        $this->categories();
        $this->builder();
        $this->groups();
        $this->translator();
        $this->events();
        $this->contracts();
        $this->artisan();
    }

    /*
     * Boot the publishing config
     */
    public function boot()
    {
        $this->config();
    }

    /**
     * Bind Notifynder
     */
    protected function notifynder()
    {
        $this->app->singleton('notifynder', function ($app) {
            return new NotifynderManager(
                $app['notifynder.category'],
                $app['notifynder.sender'],
                $app['notifynder.notification'],
                $app['notifynder.dispatcher'],
                $app['notifynder.group']
            );
        });

        // Register Facade
        $this->app->alias('notifynder', 'Notifynder');
    }

    /**
     * Bind Notifynder Categories to IoC
     */
    protected function categories()
    {
        $this->app->singleton('notifynder.category', function ($app) {
            return new CategoryManager(
                $app->make('notifynder.category.repository')
            );
        });

        $this->app->singleton('notifynder.category.repository', function ($app) {
            return new CategoryRepository(
                new NotificationCategory()
            );
        });
    }

    /**
     * Bind the notifications
     */
    protected function notifications()
    {
        $this->app->singleton('notifynder.notification', function ($app) {
            return new NotificationManager(
                $app['notifynder.notification.repository']
            );
        });

        $this->app->singleton('notifynder.notification.repository', function ($app) {

            $notificationModel = $app['config']->get('notifynder.notification_model');
            $notificationIstance = $app->make($notificationModel);

            return new NotificationRepository(
                $notificationIstance,
                $app['db']
            );
        });

        // Default store notification
        $this->app->bind('notifynder.store', 'notifynder.notification.repository');
    }

    /**
     * Bind Translator
     */
    protected function translator()
    {
        $this->app->singleton('notifynder.translator', function ($app) {
            return new TranslatorManager(
                $app['notifynder.translator.compiler'],
                $app['config']
            );
        });

        $this->app->singleton('notifynder.translator.compiler', function ($app) {
            return new Compiler(
                $app['filesystem.disk']
            );
        });
    }

    /**
     * Bind Senders
     */
    protected function senders()
    {
        $this->app->singleton('notifynder.sender', function ($app) {
             return new SenderManager(
                 $app['notifynder.sender.factory'],
                 $app['notifynder.store'],
                 $app[Container::class]
             );
        });

        $this->app->singleton('notifynder.sender.factory', function ($app) {
            return new SenderFactory(
                $app['notifynder.group'],
                $app['notifynder.category']
            );
        });
    }

    /**
     * Bind Dispatcher
     */
    protected function events()
    {
        $this->app->singleton('notifynder.dispatcher', function ($app) {
            return new Dispatcher(
                $app['events']
            );
        });
    }

    /**
     * Bind Groups
     */
    protected function groups()
    {
        $this->app->singleton('notifynder.group', function ($app) {
            return new GroupManager(
                $app['notifynder.group.repository'],
                $app['notifynder.group.category']
            );
        });

        $this->app->singleton('notifynder.group.repository', function ($app) {
            return new GroupRepository(
                new NotificationGroup()
            );
        });

        $this->app->singleton('notifynder.group.category', function ($app) {
            return new GroupCategoryRepository(
                $app['notifynder.category'],
                new NotificationGroup()
            );
        });
    }

    /**
     * Bind Builder
     */
    protected function builder()
    {
        $this->app->singleton('notifynder.builder', function ($app) {
            return new NotifynderBuilder(
                $app['notifynder.category']
            );
        });
    }

    /**
     * Contracts of notifynder
     */
    protected function contracts()
    {
        // Notifynder
        $this->app->bind(Notifynder::class, 'notifynder');

        // Repositories
        $this->app->bind(CategoryDB::class, 'notifynder.category.repository');
        $this->app->bind(NotificationDB::class, 'notifynder.notification.repository');
        $this->app->bind(NotifynderGroupDB::class, 'notifynder.group.repository');
        $this->app->bind(NotifynderGroupCategoryDB::class, 'notifynder.group.category');

        // Main Classes
        $this->app->bind(NotifynderCategory::class, 'notifynder.category');
        $this->app->bind(NotifynderNotification::class, 'notifynder.notification');
        $this->app->bind(NotifynderTranslator::class, 'notifynder.translator');
        $this->app->bind(NotifynderGroup::class, 'notifynder.group');

        // Store notifications
        $this->app->bind(StoreNotification::class, 'notifynder.store');
        $this->app->bind(NotifynderSender::class, 'notifynder.sender');
        $this->app->bind(NotifynderDispatcher::class, 'notifynder.dispatcher');
    }

    /**
     * Publish config files
     */
    protected function config()
    {
        $this->publishes([
            __DIR__.'/../config/notifynder.php' => config_path('notifynder.php'),
            __DIR__.'/../migrations/' => base_path('/database/migrations'),
        ]);

        // Set use strict_extra config option,
        // you can toggle it in the configuraiton file
        $strictParam = $this->app['config']->get('notifynder.strict_extra',false);
        NotifynderParser::setStrictExtra($strictParam);
    }

    /**
     * Register Artisan commands
     */
    protected function artisan()
    {
        // Categories
        $this->app->singleton('notifynder.artisan.category-add', function ($app) {
            return new CreateCategory(
                $app['notifynder.category']
            );
        });

        $this->app->singleton('notifynder.artisan.category-delete', function ($app) {
            return new DeleteCategory(
                $app['notifynder.category']
            );
        });

        // Groups
        $this->app->singleton('notifynder.artisan.group-add', function ($app) {
            return new CreateGroup(
                $app['notifynder.group']
            );
        });

        $this->app->singleton('notifynder.artisan.group-add-categories', function ($app) {
            return new PushCategoryToGroup(
                $app['notifynder.group'],
                new ArtisanOptionsParser()
            );
        });

        // Register commands
        $this->commands([
            'notifynder.artisan.category-add',
            'notifynder.artisan.category-delete',
            'notifynder.artisan.group-add',
            'notifynder.artisan.group-add-categories',
        ]);
    }
}
