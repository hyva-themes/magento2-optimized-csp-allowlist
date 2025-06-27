<?php

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
        if (str_contains($template, '::')) {
            $module = explode('::', $template, 2)[0];
        } else if ($params['module']) {
            $module = $params['module'];
        }
        $module && $this->usedModules->harvestModule($module);

        return [$template, $params];
    }
}