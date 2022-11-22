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

    #[Route('/', name: 'app_index', priority: 1, methods: ['GET'])]
    public function index(AdGuardHomeClient $client, $path = ''): Response
    {

        $host = new Server();
        $host->setHost('adguard.home.chazz.link')
            ->setSchema(SchemaEnum::HTTPS)
            ->setSkipSslVerify(false)
            ->setUsername('chazzbg')
            ->setPassword('1q2w3e4r');

        $client->setServer($host);

        dump(date_default_timezone_get());
        $client->status();
        return $this->render('base.html.twig', [
            'timezone'=> date_default_timezone_get()
        ]);
    }

}
