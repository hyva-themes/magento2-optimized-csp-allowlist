<?php

namespace Hyva\OptimizedCspAllowlist\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{

    public const string XMLPATH_CSP_ALLOWLIST_OPTIMIZATION_ENABLED = 'csp/allowlist/optimization_enabled';

    public function __construct(
        private ScopeConfigInterface $scopeConfig
    ) {}

    public function isAllowlistTuningEnabled(
        $scope = ScopeInterface::SCOPE_STORE,
        $scopeCode = null
    ): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XMLPATH_CSP_ALLOWLIST_OPTIMIZATION_ENABLED,
            $scope,
            $scopeCode
        );
    }

}