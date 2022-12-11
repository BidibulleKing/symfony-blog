<?php

namespace App\Controller;

use App\Entity\Post;
use DateTime;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/post", name="post")
     */
    public function index(ManagerRegistry $doctrine): JsonResponse
    {
        $entityManager = $doctrine->getManager();

        $datetime = new DateTimeImmutable();

        $post = new Post();
        $post->setTitle("Hello, Doctrine !")
            ->setBody("C'est un bel ORM...")
            ->setNbLikes(100)
            ->setCreatedAt($datetime)
            ->setPublishedAt($datetime);


        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PostController.php',
        ]);
    }
}
