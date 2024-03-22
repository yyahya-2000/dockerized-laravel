<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckProxiesRequest;
use App\Services\ProxyService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Redis;

class ProxyController extends Controller
{
    public function index(): View
    {
        return view('proxy.index');
    }

    public function checkProxies(CheckProxiesRequest $request, ProxyService $proxyService): View
    {
        $ValidatedRequestParams = $request->validated();
        $proxies = $ValidatedRequestParams['proxies'];
        $proxyServers = explode("\n", $proxies);
        [$workingProxies, $notWorkingProxies] = $proxyService->checkProxyServers($proxyServers);

        //        ProcessProxyServers::dispatch($proxyServers);
        return view('proxy.index', compact('proxies', 'workingProxies', 'notWorkingProxies'));
    }

    public function showProgress()
    {
        dd(Redis::get('result'));
        return false;
    }
}
