<?php
namespace App\IdentityProviders;

abstract class Provider {
    public string $name;

    abstract public function mapper($employee) : object;
    abstract public function getValidationRules(bool $onUpdate = false) : array;
}
