<?php

namespace Hyva\OptimizedCspAllowlist\Model;

class UsedModules
{
    private array $modules = [];
    private bool $active = false;

    public function __construct(
        private readonly Config $config
    ) {}

    public function harvestModule(string $module): void
    {
        $this->modules[$module] = true;
    }

    public function activate(): void
    {
        $this->active = true;
    }

    public function isActive(): bool
    {
        return $this->active && $this->config->isAllowlistTuningEnabled();
    }

    public function deactivate(): void
    {
        $this->active = false;
    }

    public function getModules(): array
    {
        $modules = array_keys($this->modules);
        sort($modules, SORT_NATURAL | SORT_ASC);
        return $modules;
    }

    public function getCacheId(): string
    {
        if (! $this->config->isAllowlistTuningEnabled()) {
            return 'csp_whitelist_config';
        }

        return 'csp_whitelist_config_' . strtolower(implode('_', $this->getModules()));
    }

}