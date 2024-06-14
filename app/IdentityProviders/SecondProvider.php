<?php

namespace App\IdentityProviders;

class SecondProvider extends Provider
{

    private array $mappingField = ['first_name' => 'firstName', 'last_name' => 'lastName', 'email' => 'email'];

    public function mapper($employee): object
    {
        $mapped = [];
        foreach ($employee as $key => $value) {
            if (in_array($key, array_keys($this->mappingField))) {
                $mapped[$this->mappingField[$key]] = $value;
            }
        }
        return (object)$mapped;
    }

    public function getValidationRules($onUpdate = false): array
    {
        $required = $onUpdate ? '' : 'required|';
        return [
            'email' => $required . 'email',
            'last_name' => $required . 'string',
            'first_name' => $required . 'string'
        ];
    }
}
