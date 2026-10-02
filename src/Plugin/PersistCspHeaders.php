<?php

declare(strict_types=1);

namespace Hyva\OptimizedCspAllowlist\Plugin;

use Hyva\OptimizedCspAllowlist\Model\UsedModules;
use Magento\Csp\Model\CspRenderer;
use Magento\Framework\App\PageCache\Identifier;
use Magento\Framework\App\Response\HttpInterface as HttpResponse;
use Magento\PageCache\Model\Cache\Type as FullPageCache;
use Magento\PageCache\Model\Config as FpcConfig;

/**
 * Plugin to persist CSP headers in Full Page Cache for restoration on FPC hits
 *
 * Solves the issue where Hyva_OptimizedCspAllowlist cannot detect used modules
 * on FPC hits (no template rendering), causing empty CSP headers.
 *
 * On cache miss (normal request):
 * - Templates are rendered, modules detected, CSP computed
 * - Plugin saves CSP headers to FPC after rendering
 *
 * On cache hit (FPC):
 * - Templates not rendered, modules empty, CSP would be empty
 * - Plugin restores previously saved CSP headers from FPC
 *
 *
 * @see \Hyva\OptimizedCspAllowlist\Model\UsedModules
 * @see \Magento\Framework\App\PageCache\Identifier
 * @see \Magento\PageCache\Model\Config
 */
class PersistCspHeaders
{
    /**
     * CSP HTTP header names to persist and restore
     *
     */
    private const CSP_HEADERS = [
        'Content-Security-Policy',
        'Content-Security-Policy-Report-Only',
        'Report-To',
    ];

    private const CACHE_KEY_PREFIX = 'hyva_csp_headers_';

    /**
     *
     * @var UsedModules
     */
    private UsedModules $usedModules;

    /**
     *
     * @var FullPageCache
     */
    private FullPageCache $cache;

    /**
     *
     * @var Identifier
     */
    private Identifier $identifier;

    /**
     * Full Page Cache configuration
     *
     * @var FpcConfig
     */
    private FpcConfig $fpcConfig;

    /**
     * Constructor
     *
     * @param UsedModules $usedModules
     * @param FullPageCache $cache F
     * @param Identifier $identifier
     * @param FpcConfig $fpcConfig
     */
    public function __construct(
        UsedModules $usedModules,
        FullPageCache $cache,
        Identifier $identifier,
        FpcConfig $fpcConfig
    ) {
        $this->usedModules = $usedModules;
        $this->cache = $cache;
        $this->identifier = $identifier;
        $this->fpcConfig = $fpcConfig;
    }

    /**
     * Persist or restore CSP headers after the CSP renderer has run
     *
     * Execution flow:
     * 1. Return early if Built-in FPC is disabled or Hyva optimization is inactive
     * 2. Check if FPC hit (no modules detected)
     *    - Yes: Replace the rendered CSP headers with the cached ones
     *    - No: Save the computed CSP headers to FPC for future hits
     *
     * Note: Plugin is inactive when using Varnish or when FPC is disabled.
     *
     * @param CspRenderer $subject CSP renderer instance (unused, required by plugin interface)
     * @param mixed $result Result of the original render method (void)
     * @param HttpResponse $response HTTP response to apply CSP headers to
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterRender(
        CspRenderer $subject,
        $result,
        HttpResponse $response
    ): void {
        if (!$this->isApplicable()) {
            return;
        }

        $cacheKey = $this->buildCacheKey();

        if ($this->isFpcHit()) {
            $this->restoreCachedHeaders($cacheKey, $response);
            return;
        }

        $this->persistHeaders($cacheKey, $response);
    }

    /**
     * Check if CSP headers need to be persisted or restored at all
     *
     * Criteria:
     * - Full Page Cache is enabled in Magento configuration
     * - Cache type is Built-in (not Varnish or external cache)
     * - Hyva optimization is active (module enabled)
     *
     * Note: This plugin only works with Magento Built-in FPC.
     * With Varnish or other external caching, CSP headers must be
     * handled differently (e.g., Varnish VCL configuration).
     *
     * @return bool
     */
    private function isApplicable(): bool
    {
        return $this->fpcConfig->isEnabled()
            && $this->fpcConfig->getType() === FpcConfig::BUILT_IN
            && $this->usedModules->isActive();
    }

    /**
     * Build unique cache key for current page variant
     *
     * Uses Magento's page identifier which includes:
     * - Request URL (normalized, without tracking parameters)
     * - Store ID
     * - Customer group
     * - Currency
     * - HTTP context vary string (theme, language, etc.)
     * - Secure flag (HTTP vs HTTPS)
     *
     * @return string Cache key for current page
     */
    private function buildCacheKey(): string
    {
        return self::CACHE_KEY_PREFIX . $this->identifier->getValue();
    }

    /**
     * Check if current request is a Full Page Cache hit
     *
     * Empty modules list indicates templates were not rendered,
     * which only happens on FPC hits.
     *
     * @return bool True if FPC hit, false if cache miss
     */
    private function isFpcHit(): bool
    {
        return empty($this->usedModules->getModules());
    }

    /**
     * Restore cached CSP headers to HTTP response
     *
     * Replaces the CSP headers set by the renderer, so the response only
     * contains the headers as they were computed on the original cache miss.
     *
     * @param string $cacheKey Cache key to load headers from
     * @param HttpResponse $response HTTP response to apply headers to
     * @return bool True if headers were successfully restored, false otherwise
     */
    private function restoreCachedHeaders(string $cacheKey, HttpResponse $response): bool
    {
        $serialized = $this->cache->load($cacheKey);

        if ($serialized === false || $serialized === '') {
            return false;
        }

        $headers = $this->deserializeHeaders($serialized);

        if (empty($headers)) {
            return false;
        }

        foreach (self::CSP_HEADERS as $name) {
            $response->clearHeader($name);
        }

        foreach ($headers as $name => $value) {
            if (is_string($name) && is_string($value)) {
                $response->setHeader($name, $value, true);
            }
        }

        return true;
    }

    /**
     * Persist computed CSP headers to Full Page Cache
     *
     * Extracts CSP headers from HTTP response and saves them to FPC
     * with same tag as cached page for synchronized invalidation.
     *
     * @param string $cacheKey Cache key to save headers under
     * @param HttpResponse $response HTTP response containing CSP headers
     * @return void
     */
    private function persistHeaders(string $cacheKey, HttpResponse $response): void
    {
        $headers = $this->extractHeaders($response);

        if (empty($headers)) {
            // No CSP headers to cache
            return;
        }

        $serialized = $this->serializeHeaders($headers);
        $this->cache->save($serialized, $cacheKey, [FullPageCache::CACHE_TAG]);
    }

    /**
     * Extract CSP headers from HTTP response
     *
     * @param HttpResponse $response HTTP response to extract headers from
     * @return array<string, string> Associative array of header name => header value
     */
    private function extractHeaders(HttpResponse $response): array
    {
        $headers = [];

        foreach (self::CSP_HEADERS as $name) {
            $header = $response->getHeader($name);
            if ($header !== false) {
                $headers[$name] = $header->getFieldValue();
            }
        }

        return $headers;
    }

    /**
     * Serialize headers array to string for cache storage
     *
     * @param array<string, string> $headers Headers to serialize
     * @return string JSON-encoded headers
     */
    private function serializeHeaders(array $headers): string
    {
        return json_encode($headers);
    }

    /**
     * Deserialize headers from cache storage
     *
     * @param string $serialized JSON-encoded headers
     * @return array<string, string> Deserialized headers array, empty if invalid JSON
     */
    private function deserializeHeaders(string $serialized): array
    {
        $headers = json_decode($serialized, true);

        if (!is_array($headers)) {
            return [];
        }

        return $headers;
    }
}
