<?php

namespace App\Controller;

use App\Entity\image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/image/page', name: 'ImagesPage')]
    public function getExtension(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $gallery_id = $request->query->get('gallery_id');
        $page = intval($request->query->get('page'), 10);
        $length = intval($request->query->get('length'), 10);
        $offset = $page * $length - $length;


        return new JsonResponse(
            $entityManager
                ->getRepository(image::class)
                ->findBy(criteria: ['gallery_id' => $gallery_id], limit: $length, offset: $offset)
        );
    }
}