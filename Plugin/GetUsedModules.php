<?php

namespace Hyva\OptimizedCspAllowlist\Plugin;

use Hyva\OptimizedCspAllowlist\Model\UsedModules;
use Hyva\OptimizedCspAllowlist\Module\ModuleList;

class GetUsedModules
{
    public function __construct(
        private UsedModules $usedModules,
    ) {}

    public function aroundGetNames(
        ModuleList $subject,
        callable $proceed
    ): array {
        if ($this->usedModules->isActive()) {
            return $this->usedModules->getModules();
        }

        return $proceed();
    }
}