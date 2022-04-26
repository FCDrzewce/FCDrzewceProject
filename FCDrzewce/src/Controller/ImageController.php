<?php

namespace App\Controller;

use App\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class ImageController extends AbstractController
{
    #[Route('/image/add', name: 'addImage')]
    public function getExtension(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        // IMG -> base64 => https://www.base64-image.de
        // Odebranie zdjecia w base64
        $img = $request->get('img');

        $gallery_id = $request->get('gallery_id');
        $reference = $request->get('reference');
        $description = $request->get('description');

        // Pobranie danych niezbędnych do otrzymania rozszerzenia
        // data:image/jpeg;base64 -> jpeg
        $offset = strpos($img, '/') + 1;
        $length = strpos($img, ';') - $offset;
        $ext = substr($img, $offset, $length);

        // Usunięcie z tekstu danych o pliku
        $img = str_replace('data:image/' . $ext . ';base64,', '', $img);

        // Przypisanie zmiennej $data zdekodowanego base64
        $data = base64_decode($img);

        // Wygenerowanie UUID v4
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));

        // Wygenerowanie nazwy pliku z UUID v4 i rozszerzeniem
        $file_name = $uuid . '.' . $ext;

        // Zapisanie pliku w katalogu images
        file_put_contents('../images/' . $file_name, $data);

        // Zainicjowanie encji Image i nadanie odpowiednich wartości
        $image = new Image();
        $image->setPath('../images/' . $file_name);
        $image->setGalleryId($gallery_id);
        $image->setReference($reference);
        $image->setDescription($description);

        // Przygotowanie zapytania i wysłanie go do bazy danych
        $entityManager->persist($image);
        $entityManager->flush($image);

        // Zwrócenie w Resposne komunikatu o zapisanym zdjęciu
        return new Response('Pomyślnie zapisano plik: ' . $file_name);
    }
}