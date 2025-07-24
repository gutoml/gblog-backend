<?php

namespace App\Services;

interface Service
{
    /**
     * Define the methods that all services should implement.
     */
    public function execute(array $data): mixed;
}
