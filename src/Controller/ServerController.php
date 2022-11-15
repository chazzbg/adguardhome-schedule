<?php

namespace App\Controller;

use App\ApiClient\AdGuardHomeClient;
use App\Entity\Server;
use Doctrine\Persistence\ManagerRegistry;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/server', name: 'api_server_', priority: 10)]
class ServerController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(ManagerRegistry $registry): JsonResponse
    {
        $servers = $registry->getRepository(Server::class)->findAll();

        return $this->json($servers, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['username', 'password', 'rules']
        ]);
    }

    #[Route('', name: 'create  ', methods: ['POST'])]
    public function create(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ManagerRegistry $registry,
        Request $request,
        AdGuardHomeClient $client
    ) {
        $server = new Server();
        $server->setName($request->toArray()['name'] ?? null)
            ->setHost($request->toArray()['host'] ?? null)
            ->setSkipSslVerify((bool)$request->toArray()['skip_ssl_verify'] ?? null)
            ->setSchema($request->toArray()['schema'] ?? null)
            ->setUsername($request->toArray()['username'] ?? null)
            ->setPassword($request->toArray()['password'] ?? null);


        $errors = $validator->validate($server);
        if (count($errors) === 0) {
            $client->setServer($server);
            try {
                $client->status();
            } catch (\Exception $e) {
                $errors = new ConstraintViolationList();
                $errors->add(
                    new ConstraintViolation("Unable to connect to the server! Please check again if server host and credentials are correct", '', [], $server, 'general', null)
                );
            }
        }
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }


        $manager = $registry->getManager();
        $manager->persist($server);
        $manager->flush();
        return $this->json($server, Response::HTTP_CREATED);
    }

    #[Route('/{server}', name: 'show', methods: ['GET'])]
    public function show(Server $server): JsonResponse
    {
        return $this->json($server, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['username', 'password']
        ]);
    }

    #[Route('/{server}', name: 'edit  ', methods: ['POST'])]
    public function edit(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ManagerRegistry $registry,
        Request $request,
        Server $server
    ) {
        $server = $serializer->deserialize($request->getContent(), Server::class, 'json', [
            AbstractNormalizer::OBJECT_TO_POPULATE => $server
        ]);

        $errors = $validator->validate($server);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $manager = $registry->getManager();
        $manager->persist($server);
        $manager->flush();
        return $this->json($server, 201);
    }

    #[Route('/{server}', name: 'delete  ', methods: ['DELETE'])]
    public function delete(
        ManagerRegistry $registry,
        Server $server
    ) {
        $manager = $registry->getManager();
        $manager->remove($server);
        $manager->flush();

        return new Response();
    }


    #[Route('/{server}/live', name: 'live', methods: ['GET'])]
    public function live(AdGuardHomeClient $client, Server $server): JsonResponse
    {
        $client->setServer($server);

        try {
            $client->status();
            $status = true;
        } catch (\Exception $e) {
            $status = false;
        }
        return $this->json(['status' => $status ? 'up' : 'down']);
    }

    #[Route('/{server}/clients', name: 'clients', methods: ['GET'])]
    public function clients(AdGuardHomeClient $client, Server $server): JsonResponse
    {
        $client->setServer($server);
        return $this->json($client->clients()['clients']);
    }
}
