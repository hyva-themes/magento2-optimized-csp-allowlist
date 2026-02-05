<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\OptimizedCspAllowlist\Model\Collector;

use Magento\Csp\Api\PolicyCollectorInterface;
use Magento\Csp\Model\Policy\FetchPolicy;

class ViewModelPolicyCollector implements PolicyCollectorInterface
{
    const ALLOWED_POLICIES = [
        'child-src',
        'connect-src',
        'font-src',
        'frame-src',
        'img-src',
        'manifest-src',
        'media-src',
        'object-src',
        'script-src',
        'style-src',
    ];

    private array $policies = [];


    public function collect(array $defaultPolicies = []): array
    {
        $policies = $defaultPolicies;

        foreach ($this->policies as $policyId => $values) {
            $policies[] = new FetchPolicy(
                $policyId,
                false,
                $values['hosts'],
                [],
                false,
                false,
                false,
                [],
                $values['hashes'],
                false,
                false
            );
        }

        return $policies;
    }

    public function addHosts(string $policyId, array $hosts): void
    {
        $this->addPolicy($policyId, 'hosts', $hosts);
    }

    public function addHashes(string $policyId, array $hashes): void
    {
        $this->addPolicy($policyId, 'hashes', $hashes);
    }

    private function addPolicy(string $policyId, string $type, array $data): void
    {
        $this->validatePolicy($policyId);

        $this->policies[$policyId] ??= [
            'hosts' => [],
            'hashes' => [],
        ];

        $this->policies[$policyId][$type] = array_unique(array_merge($this->policies[$policyId][$type], array_values($data)));
    }

    private function validatePolicy(string $policyId): void
    {
        if (!in_array($policyId, self::ALLOWED_POLICIES, true)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid policy id: "%s" is not an allowed policy in this context', $policyId)
            );
        }
    }
}