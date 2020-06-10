<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Money\Currency;
use Money\Money as MoneyMoney;

class Money implements CastsAttributes
{
    const CURRENCY_DEFAULT = 'USD';

    const CURRENCY_CONFIG_KEY = 'ihd.currency';

    /** @var string */
    protected static $currency;

    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return array
     */
    public function get($model, $key, $value, $attributes)
    {
        // Allow 0 but not other empty values
        if (empty($value) && $value !== 0) {
            return null;
        }

        if ($value instanceof MoneyMoney) {
            return $value;
        }

        if (!static::$currency) {
            static::$currency = new Currency(
                config(self::CURRENCY_CONFIG_KEY) ?: self::CURRENCY_DEFAULT
            );
        }

        return new MoneyMoney((int) $value, static::$currency);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  array  $value
     * @param  array  $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        // Allow 0 but not other empty values
        if (empty($value) && $value !== 0) {
            return null;
        }

        return $value instanceof MoneyMoney ? $value->getAmount() : (int) $value;
    }
}
