<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\OptimizedCspAllowlist\Model;

use Magento\Framework\Module\ModuleListInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UsedModulesTest extends TestCase
{

    private Config|MockObject $config;
    private ModuleListInterface|MockObject $moduleList;
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
        $this->moduleList = $this->createMock(ModuleListInterface::class);
        $this->moduleList->method('getAll')->willReturn([
            'Vendor_Module' => ['name' => 'Vendor_Module', 'sequence' => []],
            'Vendor_OtherModule' => ['name' => 'Vendor_OtherModule', 'sequence' => []],
            'Vendor_Child' => ['name' => 'Vendor_Child', 'sequence' => ['Vendor_Parent', 'Vendor_Sibling']],
            'Vendor_Parent' => ['name' => 'Vendor_Parent', 'sequence' => ['Vendor_Grandparent']],
            'Vendor_Sibling' => ['name' => 'Vendor_Sibling', 'sequence' => []],
            'Vendor_Grandparent' => ['name' => 'Vendor_Grandparent', 'sequence' => []],
        ]);
        $this->usedModules  = new UsedModules(
            $this->config,
            $this->moduleList
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

    public function testGetModulesShouldIncludeSequenceDependenciesOfUsedModules(): void
    {
        $this->config->method('isAllowlistOptimizationEnabled')->willReturn(true);
        $this->config->method('isAllowlistModulesDisabled')->willReturn(false);

        $this->usedModules->collect('Vendor_Child');

        $this->assertSame(
            ['Vendor_Child', 'Vendor_Grandparent', 'Vendor_Parent', 'Vendor_Sibling'],
            $this->usedModules->getModules(),
            'Direct and nested sequence dependencies should be included'
        );
    }

    public function testGetModulesShouldKeepDependenciesWhenMoreModulesAreUsedThanDependenciesExist(): void
    {
        $this->config->method('isAllowlistOptimizationEnabled')->willReturn(true);
        $this->config->method('isAllowlistModulesDisabled')->willReturn(false);

        $this->usedModules->collect('Vendor_Module');
        $this->usedModules->collect('Vendor_OtherModule');
        $this->usedModules->collect('Vendor_Sibling');
        $this->usedModules->collect('Vendor_Child');

        $this->assertSame(
            ['Vendor_Child', 'Vendor_Grandparent', 'Vendor_Module', 'Vendor_OtherModule', 'Vendor_Parent', 'Vendor_Sibling'],
            $this->usedModules->getModules(),
            'Dependencies must not be dropped because a used module already occupies the same list index'
        );
    }

    public function testGetCacheIdShouldContainSequenceDependencies(): void
    {
        $this->config->method('isAllowlistOptimizationEnabled')->willReturn(true);
        $this->config->method('isAllowlistModulesDisabled')->willReturn(false);

        $this->usedModules->collect('Vendor_Child');

        $this->assertSame(
            'csp_whitelist_config_vendor_child_vendor_grandparent_vendor_parent_vendor_sibling',
            $this->usedModules->getCacheId()
        );
    }
}
