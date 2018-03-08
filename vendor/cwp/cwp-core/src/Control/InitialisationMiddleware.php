<?php

namespace CWP\Core\Control;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Control\Middleware\HTTPMiddleware;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Environment;

/**
 * Initialises CWP-specific configuration settings, to avoid _config.php.
 */
class InitialisationMiddleware implements HTTPMiddleware
{
    use Configurable;

    /**
     * Disable the automatically added 'X-XSS-Protection' header that is added to all responses. This should be left
     * alone in most circumstances to include the header. Refer to Mozilla Developer Network for more information:
     * https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-XSS-Protection
     *
     * @config
     * @var bool
     */
    private static $xss_protection_enabled = true;

    /**
     * Enable egress proxy. This works on the principle of setting http(s)_proxy environment variables,
     *  which will be automatically picked up by curl. This means RestfulService and raw curl
     *  requests should work out of the box. Stream-based requests need extra manual configuration.
     *  Refer to https://www.cwp.govt.nz/guides/core-technical-documentation/common-web-platform-core/en/how-tos/external_http_requests_with_proxy
     *
     * @config
     * @var bool
     */
    private static $egress_proxy_default_enabled = true;

    /**
     * Configure the list of domains to bypass proxy by setting the NO_PROXY environment variable.
     * 'services.cwp.govt.nz' needs to be present for Solr and Docvert internal CWP integration.
     * 'localhost' is necessary for accessing services on the same instance such as tika-server for text extraction.
     *
     * @config
     * @var string[]
     */
    private static $egress_proxy_exclude_domains = [
        'services.cwp.govt.nz',
        'localhost',
    ];

    public function process(HTTPRequest $request, callable $delegate)
    {
        $response = $delegate($request);

        if ($this->config()->get('egress_proxy_default_enabled')) {
            $this->configureEgressProxy();
        }
        
        $this->configureProxyDomainExclusions();

        if ($this->config()->get('xss_protection_enabled') && $response) {
            $response->addHeader('X-XSS-Protection', '1; mode=block');
        }

        return $response;
    }

    /**
     * If the outbound egress proxy details have been defined in environment variables, configure the proxy
     * variables that are used to configure it.
     */
    protected function configureEgressProxy()
    {
        if (!Environment::getEnv('SS_OUTBOUND_PROXY')
            || !Environment::getEnv('SS_OUTBOUND_PROXY_PORT')
        ) {
            return;
        }

        $proxy = Environment::getEnv('SS_OUTBOUND_PROXY');
        $proxyPort = Environment::getEnv('SS_OUTBOUND_PROXY_PORT');

        Environment::setEnv('http_proxy', $proxy . ':' . $proxyPort);
        Environment::setEnv('https_proxy', $proxy . ':' . $proxyPort);
    }

    /**
     * Configure any domains that should be excluded from egress proxy rules and provide them to the environment
     */
    protected function configureProxyDomainExclusions()
    {
        $noProxy = $this->config()->get('egress_proxy_exclude_domains');
        if (empty($noProxy)) {
            return;
        }

        if (!is_array($noProxy)) {
            $noProxy = [$noProxy];
        }

        // Merge with exsiting if needed.
        if (Environment::getEnv('NO_PROXY')) {
            $noProxy = array_merge(explode(',', Environment::getEnv('NO_PROXY')), $noProxy);
        }

        Environment::setEnv('NO_PROXY', implode(',', array_unique($noProxy)));
    }
}
