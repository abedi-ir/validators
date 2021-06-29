<?php
namespace Jalno\Validators;

use Jalno\Lumen\Packages\PackageAbstract;

class Package extends PackageAbstract
{
    public function getProviders(): array
    {
        return [
            Providers\ValidatorsServiceProvider::class,
        ];
    }

    public function basePath(): string
    {
        return __DIR__;
    }

    public function getNamespace(): string
    {
        return __NAMESPACE__;
    }
}
