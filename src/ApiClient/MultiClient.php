<?php

namespace App\ApiClient;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @method array status()
 * @method array clients()
 * @method boolean blockServices(array $services)
 * @method boolean updateClient(string $name, array $services)
 */
class MultiClient
{
    private Collection $clients;
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->clients = new ArrayCollection();
        $this->client = $client;
    }

    public function setServers(Collection $servers)
    {
        $this->servers = $servers;

        foreach ($servers as $server) {
            $client = new AdGuardHomeClient($this->client);
            $client->setServer($server);
            $this->clients->add($client);
        }
    }

    public function __call(string $name, $arguments = null)
    {
        $response = [];
        /** @var AdGuardHomeClient $client */
        foreach ($this->clients as $client) {
            $response[$client->getServer()->getHost()] = $client->{$name}(...$arguments);
        }

        return $response;
    }
}