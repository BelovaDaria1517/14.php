<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(ManagerRegistry $doctrine): Response
    {
    
        $client = $this->getUser();

        $orders = $doctrine->getRepository(\App\Entity\Order::class)
            ->findBy(['client' => $client], ['createdAt' => 'DESC']);

        return $this->render('profile/index.html.twig', [
            'client' => $client,
            'orders' => $orders,
        ]);
    }
}