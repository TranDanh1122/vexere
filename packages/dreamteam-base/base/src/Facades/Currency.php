<?php

namespace DreamTeam\Base\Facades;

use DreamTeam\Base\Supports\CurrencySupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void setApplicationCurrency(\DreamTeam\Base\Models\Currency $currency)
 * @method static \DreamTeam\Base\Models\Currency|null getApplicationCurrency()
 * @method static \DreamTeam\Base\Models\Currency|null getDefaultCurrency()
 * @method static string|null getSymbol()
 * @method static \Illuminate\Support\Collection currencies()
 * @method static string|null detectedCurrencyCode()
 * @method static string|null setCurrencyWithLocale()
 * @method static currencyTextDefault()
 * @method static array countryCurrencies()
 * @method static array currencyCodes()
 *
 * @see \DreamTeam\Base\Supports\CurrencySupport
 */
class Currency extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CurrencySupport::class;
    }
}
