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
use Magento\Framework\View\Element\Template\File\Resolver;

class AggregateUsedModules
{
    public function __construct(
        private UsedModules $usedModules
    ) {}

    public function beforeGetTemplateFileName(Resolver $subject, $template, $params = []): array
    {
        $module = false;
        if (is_string($template) && str_contains($template, '::')) {
            $module = explode('::', $template, 2)[0];
        } else if ($params['module']) {
            $module = $params['module'];
        }
        $module && $this->usedModules->collect($module, $template);

        return [$template, $params];
    }
}