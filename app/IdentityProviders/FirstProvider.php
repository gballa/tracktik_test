<?php

namespace App\IdentityProviders;

class FirstProvider extends Provider
{
    private array $mappingField = ['name' => 'firstName', 'surname' => 'lastName', 'email' => 'email'];

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
            'surname' => $required . 'string',
            'name' => $required . 'string'
        ];
    }
}
