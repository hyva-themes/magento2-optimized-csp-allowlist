<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed under the BSD-3-Clause license.
 * See LICENSE.txt for details.
 */

declare(strict_types=1);

namespace Hyva\OptimizedCspAllowlist\Model\Collector;

use Magento\Csp\Model\Policy\FetchPolicy;
use PHPUnit\Framework\TestCase;

class ViewModelPolicyCollectorTest extends TestCase
{
    public function testAddHostsShouldNeverAllowedOtherPolicies()
    {
        $viewModelPolicyCollector = new ViewModelPolicyCollector;

        $this->expectException(\InvalidArgumentException::class);
        $viewModelPolicyCollector->addHosts('default-src', ['host']);

        $this->expectException(\InvalidArgumentException::class);
        $viewModelPolicyCollector->addHashes('default-src', ['hash']);
    }

    public function testAddHashesShouldNeverAllowedOtherPolicies()
    {
        $viewModelPolicyCollector = new ViewModelPolicyCollector;

        $this->expectException(\InvalidArgumentException::class);
        $viewModelPolicyCollector->addHashes('default-src', ['hash']);
    }

    public function testCollectAlsoReturnsDefaultPolicies()
    {
        $viewModelPolicyCollector = new ViewModelPolicyCollector;

        $policies = [
            'test'
        ];

        $this->assertSame('test', $viewModelPolicyCollector->collect($policies)[0]);

        $viewModelPolicyCollector->addHosts('script-src', ['host']);
        $this->assertSame('test', $viewModelPolicyCollector->collect($policies)[0]);
    }

    public function testCollectedHostsAndHashes()
    {
        $viewModelPolicyCollector = new ViewModelPolicyCollector;

        $viewModelPolicyCollector->addHosts('script-src', ['host']);
        $viewModelPolicyCollector->addHashes('script-src', ['host']);

        $this->assertContainsOnly(FetchPolicy::class, $viewModelPolicyCollector->collect());
    }

}
