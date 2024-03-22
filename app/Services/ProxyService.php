<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;

class ProxyService
{

    private const URI = 'https://example.com/';
    private const TIMEOUT = 3;
    private const CONCURRENCY = 20;

    public function checkProxyServers(array $proxyServers): array
    {
        $client = new Client([
            'timeout' => self::TIMEOUT,
            'http_errors' => false // Disable Guzzle from throwing exceptions for HTTP errors
        ]);

        $requests = $this->formulateRequests($proxyServers, $client);

        $workingProxies = [];
        $notWorkingProxies = [];
        $pool = new Pool($client, $requests(), [
            'concurrency' => self::CONCURRENCY,
            'fulfilled' => function (Response $response, $index) use (&$workingProxies, $proxyServers) {
                $workingProxies[] = $this->handleFulfilledRequests($response, $index, $proxyServers);
            },
            'rejected' => function (RequestException $reason, $index) use (&$notWorkingProxies, $proxyServers) {
                $notWorkingProxies[] = $this->handleRejectedRequests($reason, $index, $proxyServers);
            },
        ]);

        $promise = $pool->promise();
        $promise->wait(false);

        return array($workingProxies, $notWorkingProxies);
    }

    private function formulateRequests(array $proxyServers, Client $client): \Closure
    {
        return function () use ($client, $proxyServers) {
            foreach ($proxyServers as $proxyServer) {
                $proxyServer = explode(':', trim($proxyServer));
                $proxyIp = $proxyServer[0];
                $proxyPort = $proxyServer[1];

                // Define the request options
                $requestOptions = [
                    RequestOptions::PROXY => ['http' => "http://{$proxyIp}:{$proxyPort}"],
                    RequestOptions::VERIFY => false,
                ];

                yield function () use ($client, $requestOptions, $proxyIp, $proxyPort) {
                    return $client->getAsync(self::URI, $requestOptions);
                };
            }
        };
    }

    private function handleFulfilledRequests(Response $response, $index, array $proxyServers): array
    {
        // Extract information from the response
        list($proxyIp, $proxyPort) = $this->fetchIpAndPort($proxyServers[$index]);

        $viaHeader = $response->getHeaderLine('Via');
        $proxyType = str_starts_with($viaHeader, 'HTTP/') ? 'HTTP' : 'Unknown';

        // Calculate download speed via proxy
        $start = microtime(true);
        $response->getBody()->getContents();
        $end = microtime(true);
        $downloadSpeed = round(($end - $start) * 1000, 4); // convert to ms

        // Assign status based on response status code
        $statusCode = $response->getStatusCode();

        // Add the information to the responses array
        return [
            'ip' => $proxyIp,
            'port' => $proxyPort,
            'type' => $proxyType,
            'status' => $statusCode,
            'downloadSpeed' => $downloadSpeed,
        ];
    }

    private function handleRejectedRequests(RequestException $reason, $index, array $proxyServers): array
    {
        list($proxyIp, $proxyPort) = $this->fetchIpAndPort($proxyServers[$index]);

        // Add the information to the responses array
        return [
            'ip' => $proxyIp,
            'port' => $proxyPort,
            'status' => 'not working',
        ];
    }


    private function fetchIpAndPort(string $proxyServers): array
    {
        $proxyServer = explode(':', trim($proxyServers));
        $proxyIp = $proxyServer[0];
        $proxyPort = $proxyServer[1];
        return array($proxyIp, $proxyPort);
    }
}
