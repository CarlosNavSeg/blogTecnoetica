<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/category', name: 'app_admin')]
    public function categories(ManagerRegistry $doctrine, Request $request, CategoryRepository $repository): Response
    {
    $repositorio = $doctrine->getRepository(Category::class);
    $categories = $repositorio->findAll();
    $category = new Category();
    $form = $this->createForm(CategoryFormType::class, $category);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $category = $form->getData();    
        $entityManager = $doctrine->getManager();    
        $entityManager->persist($category);
        $entityManager->flush();
    }
    return $this->render('admin/category.html.twig', array(
        $repository = $doctrine->getRepository(Category::class),
        $categories = $repository->findAll(),
        'form' => $form->createView(),
        'categories' => $categories,
    ));
    }
}

