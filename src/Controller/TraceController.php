<?php

namespace App\Controller;

use App\Entity\Rule;
use App\Entity\Trace;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/trace', name: 'api_trace_', priority: 10)]
class TraceController extends AbstractController
{

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(ManagerRegistry $registry, Request $request)
    {
        $repo = $registry->getRepository(Trace::class);

        $limit = 10;
        $offset = ($request->get('page', 1) - 1) * $limit;
        $traces = $repo->findBy([], ['createdAt' => 'DESC'], $limit, $offset);

        return $this->json($traces, 200, [], [
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['rule'],

        ]);
    }

}