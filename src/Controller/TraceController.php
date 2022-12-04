<?php

namespace App\Controller;

use App\Entity\Trace;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/trace', name: 'api_trace_', priority: 10)]
class TraceController extends AbstractController
{

    const RESULTS_LIMIT = 10;

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(ManagerRegistry $registry, Request $request)
    {
        $repo = $registry->getRepository(Trace::class);

        $offset = ((int)$request->get('page', 1) - 1) * self::RESULTS_LIMIT;
        $traces = $repo->findBy([], ['createdAt' => 'DESC'], self::RESULTS_LIMIT, $offset);

        return $this->json($traces, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['rule'],
        ]);
    }

    #[Route('/total', name: 'total', methods: ['GET'])]
    public function total(ManagerRegistry $registry): JsonResponse
    {
        $repo = $registry->getRepository(Trace::class);
        return $this->json(['total' => $repo->count([])]);
    }

}