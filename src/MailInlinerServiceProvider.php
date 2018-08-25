<?php

namespace Nautiyal\LaravelMailCssInliner;

use Swift_Mailer;
use Illuminate\Support\ServiceProvider;

class MailCssInlinerServiceProvider extends ServiceProvider
{

    /**
    * Indicates if loading of the provider is deferred.
    * @var bool
    */
    protected $defer = false;

    /**
    * Bootstrap the application events.
    * @return void
    */
    public function boot()
    {
        $this->publishes(
            [ __DIR__.'/config.php' => config_path('mail-inliner.php') ], 'config'
        );
    }

    /**
    * Register services.
    * @return void
    */
    public function register()
    {
        $this->app->singleton(MailInlinerPlugin::class, function ($app) {
            $options = $app['config']->get('mail-inliner') ?: [];
            return new MailInlinerPlugin($options);
        });

        $this->app->extend('swift.mailer', function (Swift_Mailer $swiftMailer, $app) {
            $inlinerPlugin = $app->make(MailInlinerPlugin::class);
            $swiftMailer->registerPlugin($inlinerPlugin);
            return $swiftMailer;
        });
    }
}
