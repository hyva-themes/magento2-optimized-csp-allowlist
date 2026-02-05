<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\OptimizedCspAllowlist\Model;

use PHPUnit\Framework\TestCase;

class NoopPolicyCollectorTest extends TestCase
{

    public function testCollect()
    {
        $policy = new NoopPolicyCollector();

        $this->assertEmpty($policy->collect());
        $this->assertEmpty($policy->collect(['test']));
    }
}
