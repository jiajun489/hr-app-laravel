<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class TrustProxies extends Middleware
{
    protected $proxies = '*';

    protected $headers = SymfonyRequest::HEADER_X_FORWARDED_ALL;
}

