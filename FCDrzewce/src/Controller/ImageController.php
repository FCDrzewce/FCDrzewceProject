<?php

namespace App\Controller;

use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route('/image/add', name: 'addImage')]
    public function getExtension(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $file = $request->query->get('file'); // 127.0.0.1:8000/image/add?file=file.png

        $file_ext = pathinfo($file, PATHINFO_EXTENSION);
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));

        $file_name = $uuid . '.' .$file_ext;

        $image = new Image();
        $image->setPath('../images/' . $file_name);
        $image->setGalleryId(1);
        $image->setReference('referencja');
        $image->setDescription('opis');

        file_put_contents('../images/'.$file_name, $file_name);

        $entityManager->persist($image);
        $entityManager->flush($image);

        return new Response('Ext: ' . $file_name);
    }
}