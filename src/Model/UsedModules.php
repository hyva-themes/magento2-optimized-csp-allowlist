<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed under the BSD-3-Clause license.
 * See LICENSE.txt for details.
 */

declare(strict_types=1);

namespace Hyva\OptimizedCspAllowlist\Model;

class UsedModules
{
    private array $modules = [];

    public function __construct(
        private readonly Config $config
    ) {}

    public function collect(string $module, string $templateName= null): void
    {
        $this->modules[$module] ??= [];
        $this->modules[$module][] =  $templateName;
    }

    public function isActive(): bool
    {
        return $this->isConfigActive();
    }

    private function isConfigActive(): bool
    {
        return $this->config->isAllowlistOptimizationEnabled()
            || $this->config->isAllowlistModulesDisabled();
    }

    public function getModules(): array
    {
        if ($this->config->isAllowlistModulesDisabled()) {
            return [];
        }

        $modules = array_keys($this->modules);
        sort($modules, SORT_NATURAL | SORT_ASC);
        return $modules;
    }

    public function getCacheId(): string
    {
        if (! $this->isActive()) {
            return 'csp_whitelist_config';
        }

        $modules = $this->getModules();
        if (empty($modules)) {
            return 'csp_whitelist_config_none';
        }

        return 'csp_whitelist_config_' . strtolower(implode('_', $modules));
    }

}