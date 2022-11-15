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

#[Route('/api/services', name: 'api_services_', priority: 10)]
class ServicesController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(ManagerRegistry $registry, MultiClient $client): Response
    {
        $servers = $registry->getRepository(Server::class)->findAll();


        $client->setServers($servers);

        $servicesAll = $client->services();
        $services = new ArrayCollection();
        foreach ($servicesAll as $server => $list) {
            foreach ($list['blocked_services'] as $service) {
                if(!$services->contains($service)){
                    $services->add($service);
                }
            }
        }

        return $this->json($services->toArray());
    }
}