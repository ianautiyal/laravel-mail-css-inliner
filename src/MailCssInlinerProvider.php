<?php

namespace Nautiyal\MailCssInliner;

use Swift_Mailer;
use Illuminate\Support\ServiceProvider;

class MailCssInlinerProvider extends ServiceProvider
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
        $this->app->singleton(Plugin::class, function ($app) {
            $options = $app['config']->get('mail-inliner') ?: [];
            return new Plugin($options);
        });

        $this->app->extend('swift.mailer', function (Swift_Mailer $swiftMailer, $app) {
            $inlinerPlugin = $app->make(Plugin::class);
            $swiftMailer->registerPlugin($inlinerPlugin);
            return $swiftMailer;
        });
    }
}
