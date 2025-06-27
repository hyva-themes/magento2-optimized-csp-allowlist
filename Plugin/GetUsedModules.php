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