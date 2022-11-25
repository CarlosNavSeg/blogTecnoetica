<?php

namespace App\Controller;

use App\Entity\Blogpost;
use App\Entity\Category;
use App\Form\BlogpostType;
use App\Repository\BlogpostRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{

    #[Route('/blog', name: 'app_blog')]
    public function blogpost(ManagerRegistry $doctrine, Request $request, CategoryRepository $repository, BlogpostRepository $postRepository): Response
    {
    $blogpost = new Blogpost();
    $blogpost->setDate(new DateTime());
    $blogpost->setAuthor($this->getUser());
    $form = $this->createForm(BlogpostType::class, $blogpost);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $blogpost = $form->getData();    
        $entityManager = $doctrine->getManager();    
        $entityManager->persist($blogpost);
        $entityManager->flush();
        return $this->redirectToRoute('single_post', ["slug" => $blogpost->getSlug()]);
    }
    return $this->render('blog/blog.html.twig', array(
        $repository = $doctrine->getRepository(Category::class),
        $categories = $repository->findAll(),
        $postRepository = $doctrine->getRepository(Blogpost::class),
        $recents = $postRepository->findRecents(),
        'form' => $form->createView(),
        'recentposts' => $recents,
        'categories' => $categories,
    ));
    }

    #[Route('/blog/{slug}', name:'app_blog_post')]
    public function post(ManagerRegistry $doctrine, $slug): Response
    {
    $repositorio = $doctrine->getRepository(Post::class);
    $post = $repositorio->findOneBy(["slug"=>$slug]);
    return $this->render('blog/single_post.html.twig', [
        'post' => $post,
    ]);
    }
}
