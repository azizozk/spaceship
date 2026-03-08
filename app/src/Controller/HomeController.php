<?php

namespace App\Controller;

use App\Message\DemoMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'title' => 'Spaceship — Symfony + RabbitMQ',
        ]);
    }

    #[Route('/dispatch', name: 'app_dispatch', methods: ['POST'])]
    public function dispatch(MessageBusInterface $bus): Response
    {
        $bus->dispatch(new DemoMessage('Hello from Symfony Messenger via RabbitMQ!'));

        $this->addFlash('success', 'Message dispatched to RabbitMQ!');

        return $this->redirectToRoute('app_home');
    }
}
