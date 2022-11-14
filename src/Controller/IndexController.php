<?php

namespace App\Controller;

use App\ApiClient\AdGuardHomeClient;
use App\ApiClient\SchemaEnum;
use App\Entity\Server;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    #[Route('/{path}', priority: 1, name: 'app_index')]
    public function index(AdGuardHomeClient $client, $path = ''): Response
    {

        $host = new Server();
        $host->setHost('adguard.home.chazz.link')
            ->setSchema(SchemaEnum::HTTPS)
            ->setSkipSslVerify(false)
            ->setUsername('chazzbg')
            ->setPassword('1q2w3e4r');

        $client->setServer($host);

        $client->status();
        return $this->render('base.html.twig');
    }


//    #[Route('/api', priority: 10, name: 'app_indesx')]
//    public function api(): Response
//    {
//        return $this->json(['asd']);
//    }
}
