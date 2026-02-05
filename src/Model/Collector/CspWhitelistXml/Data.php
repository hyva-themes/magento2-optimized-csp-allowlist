<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes. All rights reserved.
 * See COPYING.txt for license details.
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
