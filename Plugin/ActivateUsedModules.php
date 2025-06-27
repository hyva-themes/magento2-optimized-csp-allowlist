<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed under the BSD-3-Clause license.
 * See LICENSE.txt for details.
 */

declare(strict_types=1);

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