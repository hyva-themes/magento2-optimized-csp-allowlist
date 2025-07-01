<?php

namespace Hyva\OptimizedCspAllowlist\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UsedModulesTest extends TestCase
{

    private Config|MockObject $config;
    private UsedModules $usedModules;

    protected function setUp(): void
    {
        $this->config = $this->createPartialMock(
            Config::class,
            [
                'isAllowlistOptimizationEnabled',
                'isAllowlistModulesDisabled',
            ]
        );
        $this->usedModules  = new UsedModules(
            $this->config
        );
    }

    /**
     * @dataProvider isActiveCombinations
     */
    public function testIsActive(
        bool $result,
        bool $isAllowedListOptimizationEnabled,
        bool $isAllowListOptimizationDisabled,
        string $message
    ): void {
        $config = $this->config;
        $usedModules = $this->usedModules;

        $config->method('isAllowlistOptimizationEnabled')
            ->willReturn($isAllowedListOptimizationEnabled);
        $config->method('isAllowlistModulesDisabled')
            ->willReturn($isAllowListOptimizationDisabled);

        $this->assertSame($result, $usedModules->isActive(), $message);
    }

    public static function isActiveCombinations()
    {
        return [
            [ false, false, false, 'Should not be active if config is inactive' ],
            [ true, true, false, 'Should be active if optimization is enabled' ],
            [ true, false, true, 'Should be active if disable modules flag is enabled' ],
            [ true, true, true, 'Should be active if both flags are flag is enabled' ],
        ];
    }

    public function testGetCacheIdIfOptimizationIsDisabledShouldReturnOriginalCacheId(): void
    {
        $config = $this->config;
        $usedModules = $this->usedModules;

        $config->method('isAllowlistOptimizationEnabled')
            ->willReturn(false);
        $config->method('isAllowlistModulesDisabled')
            ->willReturn(false);

        $this->assertSame('csp_whitelist_config', $usedModules->getCacheId());
        $this->assertSame('csp_whitelist_config', $usedModules->getCacheId());

        $usedModules->collect('Vendor_Module');
        $this->assertSame('csp_whitelist_config', $usedModules->getCacheId());
    }

    public function testGetCacheIdIfModulesIsDisabledShouldAlwaysBeNone(): void
    {
        $config = $this->config;
        $usedModules = $this->usedModules;

        $config->method('isAllowlistOptimizationEnabled')
            ->willReturn(false, true, false, true);
        $config->method('isAllowlistModulesDisabled')
            ->willReturn(true);

        $this->assertSame('csp_whitelist_config_none', $usedModules->getCacheId());
        $this->assertSame('csp_whitelist_config_none', $usedModules->getCacheId());

        $usedModules->collect('Vendor_Module');
        $this->assertSame('csp_whitelist_config_none', $usedModules->getCacheId());
        $usedModules->collect('Vendor_OtherModule');
        $this->assertSame('csp_whitelist_config_none', $usedModules->getCacheId());
    }

    public function testGetCacheIdIfOptimizationIsDynamicShouldAddModules(): void
    {
        $config = $this->config;
        $usedModules = $this->usedModules;

        $config->method('isAllowlistOptimizationEnabled')
            ->willReturn(true);
        $config->method('isAllowlistModulesDisabled')
            ->willReturn(false);

        $this->assertSame('csp_whitelist_config_none', $usedModules->getCacheId());

        $usedModules->collect('Vendor_Module');
        $this->assertSame('csp_whitelist_config_vendor_module', $usedModules->getCacheId());

        $usedModules->collect('Vendor_OtherModule');
        $this->assertSame('csp_whitelist_config_vendor_module_vendor_othermodule', $usedModules->getCacheId());
    }
}
