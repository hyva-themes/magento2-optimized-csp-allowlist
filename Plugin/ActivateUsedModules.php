<?php

namespace Hyva\OptimizedCspAllowlist\Plugin;

use Hyva\OptimizedCspAllowlist\Model\UsedModules;
use Magento\Csp\Model\Collector\CspWhitelistXml\FileResolver;

class ActivateUsedModules
{
    public function __construct(
        private UsedModules $usedModules,
    ) {}

    public function aroundGet(
        FileResolver $subject,
        callable $proceed,
        ...$args
    ) {
        $this->usedModules->activate();
        $result = $proceed(...$args);
        $this->usedModules->deactivate();

        return $result;
    }
}