<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment", name="app_comment")
     */
    public function index(): Response
    {
        return $this->render('comment/index.html.twig', [
            'controller_name' => 'CommentController',
        ]);
    }

    /**
     * @Route("/comment/new/{id}", name="comment_new")
     *
     * @return Response
     */
    public function new(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $comment = new Comment();
        $comment->setPost($doctrine->getRepository(Post::class)->find($id));

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute("post_show", [
                'id' => $id
            ]);
        }

        return $this->render("comment/new.html.twig", [
            'form' => $form->createView(),
            'id' => $id
        ]);
    }
}
