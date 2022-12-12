<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use DateTimeImmutable;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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


    /** //! Deprecated : use the "new" function.
     * 
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

        if ($this->isPost($post, $id)) {
            return $this->render("main/post.html.twig", [
                "post" => $post
            ]);
        }
    }

    /**
     * @Route("/post/{id}/like", name="post_like")
     *
     * @param [integer] $id
     * @return RedirectResponse
     */
    public function like($id, ManagerRegistry $doctrine): RedirectResponse
    {
        $repository = $doctrine->getRepository(Post::class);
        $manager = $doctrine->getManager();
        $datetime = new DateTimeImmutable();

        $post = $repository->find($id);

        if ($this->isPost($post, $id)) {
            $post->setNbLikes($post->getNbLikes() + 1);
            $post->setUpdatedAt($datetime);

            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute("post_show", ["id" => $id]);
        }
    }

    /**
     * @Route("new", name="post_new")
     *
     * @param Request $request
     * @return Response
     */
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        $post = new Post();

        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute("post_index");
        }

        return $this->render("main/new.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/post/{id}/delete", name="post_delete")
     *
     * @param [integer] $id
     * @return RedirectResponse
     */
    public function delete($id, ManagerRegistry $doctrine): RedirectResponse
    {
        $entityManager = $doctrine->getManager();
        $repositoryManager = $doctrine->getRepository(Post::class);

        $post = $repositoryManager->find($id);

        if ($this->isPost($post, $id)) {
            $entityManager->remove($post);
            $entityManager->flush();

            return $this->redirectToRoute("post_index");
        }
    }

    /**
     * @Route("/change-theme", name="change_theme")
     *
     * @return RedirectResponse
     */
    public function changeTheme(Request $request): RedirectResponse
    {
        $session = new Session();
        $session->start();

        if ($session->get("theme") !== null) {
            if ($session->get("theme") === "dark") {
                $session->set("theme", "light");
            } else {
                $session->set("theme", "dark");
            }
        } else {
            $session->set("theme", "dark");
        }

        $referer = $request->headers->get("referer");

        return new RedirectResponse($referer);
    }

    // * UTILS
    private function isPost($post, $id)
    {
        if (!$post) {
            throw $this->createNotFoundException(
                "Il n'y a pas de post correspondant à cet identifiant : $id"
            );
        }

        return true;
    }
}
