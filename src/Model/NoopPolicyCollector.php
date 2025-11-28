<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed under the BSD-3-Clause license.
 * See LICENSE.txt for details.
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