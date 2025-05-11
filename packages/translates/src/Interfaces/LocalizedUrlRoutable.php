<?php

namespace DreamTeam\Translate\Interfaces;

interface LocalizedUrlRoutable
{
    /**
     * Get the value of the model's localized route key.
     */
    public function getLocalizedRouteKey(string|null $locale): string|null;
}
