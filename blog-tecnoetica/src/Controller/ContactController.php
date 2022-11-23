<?php

namespace App\Controller;

use App\Entity\ContactRequest;
use App\Form\ContactRequestType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(ManagerRegistry $doctrine, HttpFoundationRequest $request): Response
    {
    $contact = new ContactRequest();
    $form = $this->createForm(ContactRequestType::class, $contact);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $contacto = $form->getData();    
        $entityManager = $doctrine->getManager();    
        $entityManager->persist($contacto);
        $entityManager->flush();
        return $this->redirectToRoute('app_sucessful_contact', []);
    }
    return $this->render('contact/contact.html.twig', array(
        'form' => $form->createView()    
    ));
    }

    #[Route('/contactSucessfull', name:'app_sucessful_contact')]
    public function contactedSucessfully() {
        return $this->render('contact/contactedsuccessfully.html.twig');
    }
}
