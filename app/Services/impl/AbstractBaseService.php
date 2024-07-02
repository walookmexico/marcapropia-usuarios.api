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
    protected function __construct() {}

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

    /**
     * Deshabilita la clonación de la instancia.
     */
    private function __clone() {}

    public function __wakeup() {}
}