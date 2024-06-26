<?php

namespace App\Services\Impl;

abstract class AbstractBaseService{
    /**
     * Instance
     *
     * @var AbstractBaseService
     */
    protected static $_instance;

    /**
     * Constructor
     *
     * @return void
     */
    private function __construct() {}

    /**
     * Get instance
     *
     * @return AbstractBaseService
     */
    public final static function getInstance() {
        if (null === static::$_instance) {
            static::$_instance = new static();
        }

        return static::$_instance;
    }
}