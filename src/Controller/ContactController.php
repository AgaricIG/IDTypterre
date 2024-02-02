<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Form\ContactFormType;
use App\Service\Mailer;

class ContactController extends AbstractController
{
/**
     * @Route("/contact", name="app_contact")
     */
    public function contact(Request $request, Mailer $mailer): Response
    {
        $form = $this->createForm(ContactFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
          if ($form->isValid()) {
            $email = $form->get('email')->getData();
            $content = $form->get('content')->getData();
            $mailer->sendContactMessage($email, $content);
            $this->addFlash("success", "Your message has been sended !");
          }
          else {
            $this->addFlash("danger", "Your form is invalid... Please retry.");
          }
        }

        return $this->render('pages/contact.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}