<?php

namespace App\ApiClient;

use App\Entity\Server;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AdGuardHomeClient
{
    private Server $server;
    private HttpClientInterface $client;
    private $clients;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param Server $server
     */
    public function setServer(Server $server): void
    {
        $this->server = $server;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }


    private function buildUrl()
    {
        return sprintf(
            "%s://%s",
            $this->server->getSchema(),
            $this->server->getHost()
        );
    }

    public function doGetRequest($endpoint): ResponseInterface
    {
        return $this->client->request(
            'GET', $this->buildUrl() . '/control' . $endpoint, [
                'auth_basic' => [$this->server->getUsername(), $this->server->getPassword()],
                'verify_peer' => $this->server->isSkipSslVerify()
            ]
        );
    }

    public function doPostRequest(string $endpoint, array $body): ResponseInterface
    {
        return $this->client->request(
            'POST', $this->buildUrl() . '/control' . $endpoint, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'auth_basic' => [$this->server->getUsername(), $this->server->getPassword()],
                'verify_peer' => $this->server->isSkipSslVerify(),
                'body' => json_encode($body)
            ]
        );
    }

    public function status(): array
    {
        $response = $this->doGetRequest('/status');

        return $response->toArray();
    }

    public function clients(): array
    {
        if(!$this->clients){
            $this->clients = $this->doGetRequest('/clients')->toArray();
        }
        return $this->clients;
    }

    public function listBlockedServices(): array
    {
        return $this->doGetRequest('/blocked_services/list')->toArray();
    }

    public function blockServices(array $services): bool
    {
        $response = $this->doPostRequest('/blocked_services/set', $services);

        return $response->getStatusCode() === 200;
    }

    public function updateClient(string $name, array $data): bool
    {
        $body = [
            'name' => $name,
            'data' => $data
        ];
        $response = $this->doPostRequest('/clients/update', $body);
        return $response->getStatusCode() === 200;
    }

    public function getClient(string $name)
    {
        $clients = $this->clients();

        foreach ($clients['clients'] as $client) {
            if ($client['name'] === $name) {
                return $client;
            }
        }

        return null;
    }

    public function services(): array
    {
        return $this->doGetRequest('/blocked_services/all')->toArray();
    }
}