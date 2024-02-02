<?php
namespace App\Controller\Admin;

use App\Tool\Paginator;
use App\Entity\Application;
use App\Form\Admin\ApplicationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppliAdminController extends AbstractController
{

    /**
    * @Route("/admin/appli", name="admin_appli")
    */
    public function index(Request $request, EntityManagerInterface $em)
    {
        $search = $request->query->get('search', "");
        $page = $request->query->get('page', 1);
        $perPage = 10;

        $applis = $em->getRepository(Application::class)->index($page, $perPage, $search);
        $pagination = new Paginator($page, $perPage, count($applis));

        return $this->render('admin/appli/list.html.twig', [
            'applis' => $applis,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/appli/{id}/edit", name="admin_appli_edit")
     */
    public function edit(Application $appli, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ApplicationFormType::class, $appli);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($appli);
            $em->flush();
            $this->addFlash('success', "L'application a bien été modifiée !");
            return $this->redirectToRoute('admin_appli');
        }

        return $this->render('admin/appli/edit.html.twig', [
            'form' => $form->createView(),
            'appli' => $appli,
        ]);
    }

    /**
     * @Route("/admin/appli/create", name="admin_appli_create")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $appli = new Application();
        $form = $this->createForm(ApplicationFormType::class, $appli);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($appli);
            $em->flush();
            $this->addFlash('success', "L'application a bien été crée !");
            return $this->redirectToRoute('admin_appli');
        }

        return $this->render('admin/appli/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/appli/{id}/delete", name="admin_appli_delete")
     */
    public function delete(Application $appli, Request $request): Response
    {
        return $this->render('admin/appli/delete.html.twig', [
            'appli' => $appli,
        ]);
    }

    /**
     * @Route("/admin/appli/{id}/delete/confirmed", name="admin_appli_delete_confirmed")
     */
    public function deleteConfirmed(Application $appli, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($appli);
        $em->flush();

        $this->addFlash('success', "L'application a été supprimée...");

        return $this->redirectToRoute('admin_appli');
    }
}
