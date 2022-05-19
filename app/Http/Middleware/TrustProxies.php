<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Http\Middleware\TrustProxies as Middleware;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array
     */
    protected $proxies = '*';

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    protected function setTrustedProxyIpAddresses(Request $request)
    {

        $clientIps = explode(',', $request->headers->get('X_FORWARDED_FOR'));

        foreach ($clientIps as $key => $clientIp) {
            $clientIps[$key] = trim($clientIp);
        }

        $clientIps[] = $request->server->get('REMOTE_ADDR');
        array_shift($clientIps);

        $request->setTrustedProxies($clientIps, $this->getTrustedHeaderNames());
    }
}
