<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'addUser')]
    public function addUser(Request $request, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $username = $request->query->get('username'); // localhost/user?username=user
        $password = $request->query->get('password');// localhost/user?password=haslo
        $h_pass = password_hash($password, PASSWORD_DEFAULT);

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($h_pass);

        $entityManager->persist($user);

        $entityManager->flush($user);

        return new Response('Saved new product with id ' . $username);
    }
}