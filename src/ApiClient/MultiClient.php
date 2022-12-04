<?php

namespace App\ApiClient;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @method array status()
 * @method array clients()
 * @method array getClient(string $name)
 * @method array services()
 * @method array listBlockedServices()
 * @method array blockServices(array $services)
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

    public function setServers(Collection|array $servers)
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


    public function updateClient(string $name, array $data): array
    {

        $response = [];

        /** @var AdGuardHomeClient $client */
        foreach ($this->clients as $client) {
            $host = $client->getServer()->getHost();
            $response[$host] = $client->updateClient(
                $name,
                $data[$host]
            );
        }

        return $response;
    }
}