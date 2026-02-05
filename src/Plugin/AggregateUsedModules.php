<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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