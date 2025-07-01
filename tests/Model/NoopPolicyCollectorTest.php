<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed under the BSD-3-Clause license.
 * See LICENSE.txt for details.
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
