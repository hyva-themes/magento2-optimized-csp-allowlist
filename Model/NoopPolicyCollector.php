<?php

namespace Hyva\OptimizedCspAllowlist\Model;

use Magento\Csp\Api\PolicyCollectorInterface;

class NoopPolicyCollector implements PolicyCollectorInterface
{

    /**
     * @inheritDoc
     */
    public function collect(array $defaultPolicies = []): array
    {
        return [];
    }
}