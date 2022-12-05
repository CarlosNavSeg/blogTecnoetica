<?php

namespace App\Controller;

use App\Entity\Blogpost;
use App\Entity\Category;
use App\Entity\Comment;
use App\Form\BlogpostType;
use App\Form\CommentFormType;
use App\Repository\BlogpostRepository;
use App\Repository\CategoryRepository;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class BlogController extends AbstractController
{

    #[Route('/blog', name: 'app_blog')]
    public function blogpost(ManagerRegistry $doctrine, Request $request, CategoryRepository $repository, BlogpostRepository $postRepository, SluggerInterface $slug): Response
    {
    $blogpost = new Blogpost();
    $blogpost->setDate(new DateTime());
    $blogpost->setAuthor($this->getUser());
    $repository = $doctrine->getRepository(Category::class);
    $categories = $repository->findAll();
    $postRepository = $doctrine->getRepository(Blogpost::class);
    $recents = $postRepository->findRecents();
    $form = $this->createForm(BlogpostType::class, $blogpost);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $blogpost = $form->getData();    
        $blogpost->setSlug($slug->slug($blogpost->getTitle()));
        $entityManager = $doctrine->getManager();    
        $entityManager->persist($blogpost);
        $entityManager->flush();
        return $this->redirectToRoute('app_blog_post', ["slug" => $blogpost->getSlug()]);
    }
    return $this->render('blog/blog.html.twig', array(
        'form' => $form->createView(),
        'blogposts' => $recents,
        'categories' => $categories,
    ));
    }

    #[Route('/blog/{slug}', name:'app_blog_post')]
    public function post(ManagerRegistry $doctrine, Request $request, $slug): Response
    {

        $repository = $doctrine->getRepository(Blogpost::class);
        $blogpost = $repository->findOneBy(["Slug"=>$slug]);
        $comments = $blogpost->getComments();
        $comment = new Comment();
        $comment->setUser($this->getUser());
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        $comment = $form->getData();
        $comment->setPost($blogpost);  
        $entityManager = $doctrine->getManager();    
        $entityManager->persist($comment);
        $entityManager->flush();
        return $this->redirectToRoute('app_blog_post', ["slug" => $blogpost->getSlug()]);
    }
    return $this->render('blog/single_post.html.twig', [
        'comments' => $comments,
        'blogpost' => $blogpost,
        'commentForm' => $form->createView()
    ]);
    }

    #[Route('/single_post/{slug}/like', name: 'post_like')]
    public function like(ManagerRegistry $doctrine, $slug): Response
    {
    $repository = $doctrine->getRepository(Post::class);
    $post = $repository->findOneBy(["slug"=>$slug]);
    if ($post){
        $post->setNumLikes($post->getNumLikes() + 1);
        $entityManager = $doctrine->getManager();    
        $entityManager->persist($post);
        $entityManager->flush();
    }
    return $this->redirectToRoute('single_post', ["slug" => $post->getSlug()]);
    }
}

