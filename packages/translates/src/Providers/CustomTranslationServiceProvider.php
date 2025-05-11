<?php

namespace DreamTeam\Translate\Providers;

use Illuminate\Translation\Translator;
use Illuminate\Support\ServiceProvider;
use DreamTeam\Translate\Facades\Language;

class CustomTranslationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->booted(function () {
            if ($this->isInAdmin()) {
                $this->app->extend('translator', function (Translator $translator) {
                    $translator->setLocale(Language::getAminpannelLocale());
                    return $translator;
                });
            }
        });
    }

    protected function isInAdmin() // clone from is_in_admin in BaseHelper class
    {
        $prefix = config('app.admin_dir');

        $segments = array_slice(request()->segments(), 0, count(explode('/', $prefix)));

        $isInAdmin = implode('/', $segments) === $prefix;

        return $isInAdmin;
    }
}
