<?php

namespace App\Controller;

use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/images', name: 'ImagesPagination')]
    public function getImages(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        // http://localhost:8000/images?gallery_id=1&page=2&length=3
        $entityManager = $doctrine->getManager();

        $gallery_id = intval($request->query->get('gallery_id'));
        $page = intval($request->query->get('page'), 10);
        $length = intval($request->query->get('length'), 10);
        $offset = $page * $length - $length;

        return new JsonResponse(
            $entityManager
                ->getRepository(Image::class)
                ->findBy(criteria: ['gallery_id' => $gallery_id], limit: $length, offset: $offset)
        );
    }
}