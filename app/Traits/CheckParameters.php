<?php

namespace App\Traits;

trait CheckParameters
{

    public function checkParamsExist(array $keys, array $data): bool
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $data)) {
                throw new \Exception("Parameter $key is required");
            }
        }
        return true;
    }
}