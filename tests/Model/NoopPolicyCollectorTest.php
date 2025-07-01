<?php

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
