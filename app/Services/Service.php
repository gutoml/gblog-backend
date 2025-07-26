<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

interface Service
{
    /**
     * Define the methods that all services should implement.
     */
    public function execute(array $data): mixed;
}
