<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed under the BSD-3-Clause license.
 * See LICENSE.txt for details.
 */

declare(strict_types=1);

namespace Hyva\OptimizedCspAllowlist\Model;

use Magento\Framework\Module\ModuleListInterface;

class UsedModules
{
    private array $modules = [];

    public function __construct(
        private readonly Config $config,
        private readonly ModuleListInterface $moduleList,
    ) {}

    public function collect(string $module, ?string $templateName = null): void
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

        // Add dependencies
        $modules += $this->getModuleDependencies($modules);

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

    private function getModuleDependencies(array $modules): array
    {
        $allModules = $this->moduleList->getAll();

        $allDependencies = [];
        foreach ($modules as $module) {
            if (! isset($allModules[$module]['sequence'])) {
                continue;
            }

            $allDependencies += $allModules[$module]['sequence'];
        }

        return array_unique($allDependencies);
    }

}
