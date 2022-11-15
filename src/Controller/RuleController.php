<?php

namespace App\Controller;

use App\Entity\Rule;
use App\Entity\Server;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/rule', name: 'api_rule_', priority: 10)]
class RuleController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(ManagerRegistry $registry): Response
    {
        $servers = $registry->getRepository(Rule::class)->findAll();

        return $this->json($servers, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['servers','traces'],
            AbstractNormalizer::CALLBACKS => [
                'time' => [$this,'convertTimeCallback'],
            ],
        ]);
    }


    #[Route('', name: 'create  ', methods: ['POST'])]
    public function create(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ManagerRegistry $registry,
        Request $request
    ): JsonResponse {
        return $this->storeRule($serializer, $request, $registry, $validator);
    }

    #[Route('/{rule}', name: 'show', methods: ['GET'])]
    public function show(Rule $rule): JsonResponse
    {
        return $this->json($rule, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['rules', 'username', 'password']
        ]);
    }

    #[Route('/{rule}', name: 'edit  ', methods: ['POST'])]
    public function edit(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        ManagerRegistry $registry,
        Request $request,
        Rule $rule
    ): JsonResponse {
        return $this->storeRule($serializer, $request, $registry, $validator, $rule);
    }

    #[Route('/{rule}', name: 'delete  ', methods: ['DELETE'])]
    public function delete(
        ManagerRegistry $registry,
        Rule $rule
    ): Response {
        $manager = $registry->getManager();
        $manager->remove($rule);
        $manager->flush();
        return new Response();
    }


    /**
     * @param SerializerInterface $serializer
     * @param Request $request
     * @param ManagerRegistry $registry
     * @param ValidatorInterface $validator
     * @param Rule|null $rule
     * @return JsonResponse
     */
    public function storeRule(
        SerializerInterface $serializer,
        Request $request,
        ManagerRegistry $registry,
        ValidatorInterface $validator,
        ?Rule $rule = null
    ): JsonResponse {
        /** @var Rule $rule */
        $context = [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['servers']
        ];

        if ($rule) {
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $rule;
        }

        $rule = $serializer->deserialize($request->getContent(), Rule::class, 'json', $context);
        $manager = $registry->getManager();
        $serverRepo = $manager->getRepository(Server::class);

        $rule->getServers()->clear();

        foreach ($request->toArray()['servers'] as $item) {
            $rule->addServer($serverRepo->find($item['id']));
        }

        $errors = $validator->validate($rule);

        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        $manager = $registry->getManager();
        $manager->persist($rule);
        $manager->flush();
        return $this->json($rule, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['rules', 'username','password']
        ] );
    }

    public function convertTimeCallback($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
        return $innerObject instanceof \DateTime ? $innerObject->format('H:i:s') : '';
    }
}
