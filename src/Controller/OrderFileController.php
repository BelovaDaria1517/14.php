<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderFile;
use App\Form\OrderFileType;
use App\Repository\OrderFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/order/{orderId}/file')]
class OrderFileController extends AbstractController
{
    #[Route('/new', name: 'app_order_file_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager,
        int $orderId
    ): Response {
        $order = $entityManager->getRepository(Order::class)->find($orderId);
        
        if (!$order) {
            throw $this->createNotFoundException('Заказ не найден');
        }

        $orderFile = new OrderFile();
        $orderFile->setOrder($order);
        
        $form = $this->createForm(OrderFileType::class, $orderFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Устанавливаем оригинальное имя файла
            if ($orderFile->getFile()) {
                $orderFile->setOriginalName($orderFile->getFile()->getClientOriginalName());
                $orderFile->setFileSize($orderFile->getFile()->getSize());
                $orderFile->setFileType($orderFile->getFile()->getMimeType());
            }

            $entityManager->persist($orderFile);
            $entityManager->flush();

            $this->addFlash('success', 'Файл успешно загружен');
            return $this->redirectToRoute('app_order_show', ['id' => $orderId]);
        }

        return $this->render('order_file/new.html.twig', [
            'order_file' => $orderFile,
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/download', name: 'app_order_file_download', methods: ['GET'])]
    public function download(OrderFile $orderFile): BinaryFileResponse
    {
        $filePath = $this->getParameter('kernel.project_dir') . '/public/files/orders/' . $orderFile->getFileName();
        
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('Файл не найден');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $orderFile->getOriginalName()
        );

        return $response;
    }

    #[Route('/{id}', name: 'app_order_file_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        OrderFile $orderFile, 
        EntityManagerInterface $entityManager
    ): Response {
        $orderId = $orderFile->getOrder()->getId();
        
        if ($this->isCsrfTokenValid('delete'.$orderFile->getId(), $request->request->get('_token'))) {
            $entityManager->remove($orderFile);
            $entityManager->flush();
            
            $this->addFlash('success', 'Файл успешно удален');
        }

        return $this->redirectToRoute('app_order_show', ['id' => $orderId]);
    }
}