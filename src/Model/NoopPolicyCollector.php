<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\OptimizedCspAllowlist\Model;

use Magento\Csp\Api\PolicyCollectorInterface;

/**
 * @deprecated Unused since 1.1.0: Will be removed in the future
 */
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