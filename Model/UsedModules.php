<?php

namespace Hyva\OptimizedCspAllowlist\Model;

class UsedModules
{
    private array $modules = [];
    private bool $active = false;

    public function __construct(
        private readonly Config $config
    ) {}

    public function collect(string $module, string $templateName= null): void
    {
        $this->modules[$module] ??= [];
        $this->modules[$module][] =  $templateName;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function isActive(): bool
    {
        return $this->active && $this->config->isAllowlistOptimizationEnabled();
    }

    public function deactivate(): void
    {
        $this->active = false;
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
        if (! $this->config->isAllowlistOptimizationEnabled()) {
            return 'csp_whitelist_config';
        }

        return 'csp_whitelist_config_' . strtolower(implode('_', $this->getModules()));
    }

}