<?php

namespace App\Controller;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class ImageController extends AbstractController
{
    public const FILES_PATH = '../images/';

    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getExtension($base64): string
    {
        // Pobranie danych niezbędnych do otrzymania rozszerzenia
        // data:image/jpeg;base64 -> jpeg
        if (str_contains($base64, 'data:image')) {
            $offset = strpos($base64, '/') + 1;
            $length = strpos($base64, ';') - $offset;
            return substr($base64, $offset, $length);
        } else {
            $baseExt = substr($base64, 0, 1);
            return match ($baseExt) {
                '/' => 'jpg',
                'i' => 'png',
                'R' => 'gif',
                'U' => 'webp',
                default => '',
            };
        }
    }

    public function generateFileName($ext): string
    {
        // Wygenerowanie UUID v4
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
        // Wygenerowanie nazwy pliku z UUID v4 i rozszerzeniem
        return $uuid . '.' . $ext;
    }

    public function renderImageFromBase($base64, $ext): string
    {
        // Usunięcie z tekstu danych o pliku
        $base64 = str_replace('data:image/' . $ext . ';base64,', '', $base64);
        // Przypisanie zmiennej $data zdekodowanego base64
        return base64_decode($base64);
    }

    public function saveImage($file_name, $image)
    {
        // Zapisanie pliku w katalogu images
        file_put_contents(self::FILES_PATH . $file_name, $image);
    }

    public function saveImageToDatabase($file_name, $gallery_id, $reference, $description) {
        // Zainicjowanie encji Image i nadanie odpowiednich wartości
        $image = new Image();
        $image->setPath(self::FILES_PATH . $file_name);
        $image->setGalleryId($gallery_id);
        $image->setReference($reference);
        $image->setDescription($description);

        // // Przygotowanie zapytania i wysłanie go do bazy danych
        $this->entityManager->persist($image);
        $this->entityManager->flush($image);
    }

    #[Route('/image/add', name: 'addImage')]
    public function addImage(Request $request, ManagerRegistry $doctrine): JsonResponse
    {
        // IMG -> base64 => https://www.base64-image.de
        // Odebranie zdjecia w base64
        $img = $request->get('img');

        $gallery_id = $request->get('gallery_id');
        $reference = $request->get('reference');
        $description = $request->get('description');

        $ext = $this->getExtension($img);
        $file_name = $this->generateFileName($ext);
        $image = $this->renderImageFromBase($img, $ext);
        $this->saveImage($file_name, $image);

        // Zainicjowanie encji Image i nadanie odpowiednich wartości
        $image = new Image();
        $image->setPath(self::FILES_PATH . $file_name);
        $image->setGalleryId($gallery_id);
        $image->setReference($reference);
        $image->setDescription($description);

        // Przygotowanie zapytania i wysłanie go do bazy danych
        $this->saveImageToDatabase($file_name, $gallery_id, $reference, $description);

        // Zwrócenie w JsonResposne danych o zapisanym zdjęciu
        return new JsonResponse(array('gallery_id' => $gallery_id, 'reference' => $reference, 'description' => $description, 'path' => self::FILES_PATH.$file_name));
    }
}