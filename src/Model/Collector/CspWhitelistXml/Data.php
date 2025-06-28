<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2022-present. All rights reserved.
 * This product is licensed under the BSD-3-Clause license.
 * See LICENSE.txt for details.
 */

declare(strict_types=1);

namespace Hyva\OptimizedCspAllowlist\Model\Collector\CspWhitelistXml;

use Hyva\OptimizedCspAllowlist\Model\UsedModules;
use Magento\Csp\Model\Collector\CspWhitelistXml\Reader;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Config\Data\Scoped;
use Magento\Framework\Config\ScopeInterface;
use Magento\Framework\Config\CacheInterface;

/**
 * Provides CSP whitelist configuration
 */
class Data extends Scoped
{
    protected $_scopePriorityScheme = ['global'];

    public function __construct(
        Reader              $reader,
        ScopeInterface      $configScope,
        CacheInterface      $cache,
        SerializerInterface $serializer,
        UsedModules         $usedModules,
    ) {
        parent::__construct(
            $reader,
            $configScope,
            $cache,
            $usedModules->getCacheId(),
            $serializer
        );
    }
}
