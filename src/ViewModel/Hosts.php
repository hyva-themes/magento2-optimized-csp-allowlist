<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hyva\OptimizedCspAllowlist\ViewModel;

use Hyva\OptimizedCspAllowlist\Model\Collector\ViewModelPolicyCollector;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Hosts implements ArgumentInterface
{
    public function __construct(
        private readonly ViewModelPolicyCollector $allowList,
    ) {}

    public function add(string $policyId, array $domains = []): void
    {
        $this->allowList->addHosts($policyId, $domains);
    }
}
