<?php

namespace Hyva\OptimizedCspAllowlist\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{

    private ScopeConfigInterface|MockObject $storeConfig;
    private Config $config;

    protected function setUp(): void
    {
        $this->storeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->config = new Config($this->storeConfig);
    }

    public function testIsAllowlistOptimizationEnabled()
    {
        $storeConfig = $this->storeConfig;
        $config = $this->config;

        $storeConfig->expects($this->once())
            ->method('getValue')
            ->with(Config::XMLPATH_CSP_ALLOWLIST_OPTIMIZATION_ENABLED)
            ->willReturn(true);
        $this->assertTrue($config->isAllowlistOptimizationEnabled());
    }

    public function testIsAllowlistModulesDisabled()
    {
        $storeConfig = $this->storeConfig;
        $config = $this->config;

        $storeConfig->expects($this->once())
            ->method('getValue')
            ->with(Config::XMLPATH_CSP_ALLOWLIST_MODULES_DISABLED)
            ->willReturn(true);
        $this->assertTrue($config->isAllowlistModulesDisabled());
    }
}
