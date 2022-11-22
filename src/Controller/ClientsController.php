<?php

namespace App\Controller;
use App\ApiClient\MultiClient;
use App\Entity\Rule;
use App\Entity\Server;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/clients', name: 'api_clients_', priority: 10)]
class ClientsController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(ManagerRegistry $registry, MultiClient $client): Response
    {
        $servers = $registry->getRepository(Server::class)->findAll();
        $client->setServers($servers);

        $clientsAll = $client->clients();
        $clients = new ArrayCollection();
        foreach ($clientsAll as $serverClients) {
            foreach ($serverClients['clients'] as $client) {
                $parts = [
                    'name'=> $client['name'],
                    'ids'=> $client['ids']
                ];
                if(!$clients->contains($parts)){
                    $clients->add($parts);
                }
            }
        }

        return $this->json($clients->toArray());
    }
}