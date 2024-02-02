<?php
namespace App\Controller;

use App\Entity\Ucs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{

    /**
    * @Route("/", name="home")
    */
    public function home()
    {
        return $this->redirectToRoute('app_login');
    }

}