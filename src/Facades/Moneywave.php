<?php
/**
 * Created by PhpStorm.
 * User: WF-INNOVATION
 * Date: 12/28/2016
 * Time: 10:46 AM
 */

namespace Samsoft\Moneywave\Facades;


use Illuminate\Support\Facades\Facade;

class Moneywave extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-moneywave';
    }

}