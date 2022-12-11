<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    /**
     * @Route("/posts", name="post_index")
     *
     * @return Response
     */
    public function index(PostRepository $postRepository): Response
    {
        $posts = $postRepository->findAll();

        return $this->render("main/index.html.twig", [
            "posts" => $posts
        ]);
    }


    /**
     * @Route("/post/create", name="post_create")
     */
    public function create(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $datetime = new DateTimeImmutable();

        $post = new Post();
        $post->setTitle("Mon troisième article.")
            ->setBody("Je vous l'ai dit : je ne m'arrête plus !")
            ->setNbLikes(2)
            ->setCreatedAt($datetime)
            ->setPublishedAt($datetime);

        $entityManager->persist($post);
        $entityManager->flush();


        return $this->render("main/index.html.twig", [
            "post" => $post
        ]);
    }

    /**
     * @Route("/post/{id}", name="post_show")
     *
     * @return Response
     */
    public function show($id, ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Post::class);

        $post = $repository->find($id);

        return $this->render("main/post.html.twig", [
            "post" => $post
        ]);
    }
}
