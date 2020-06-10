<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Money\Currency;
use Money\Money as MoneyMoney;

class Money implements CastsAttributes
{
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
        if (!static::$currency) {
            static::$currency = new Currency(config('indiehd.currency', 'USD'));
        }

        return $value !== null ?
            new MoneyMoney($value, static::$currency)
            : null;
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
        /** @var MoneyMoney $value */
        return $value !== null ? $value->getAmount() : null;
    }
}
