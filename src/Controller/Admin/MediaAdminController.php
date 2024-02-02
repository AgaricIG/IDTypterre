<?php
namespace App\Controller\Admin;

use App\Tool\Paginator;
use App\Entity\Media;
use App\Entity\Question;
use App\Form\Admin\MediaFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MediaAdminController extends AbstractController
{
    /**
    * @Route("/admin/media", name="admin_media")
    */
    public function index(Request $request, EntityManagerInterface $em)
    {
        $search = $request->query->get('search', "");
        $page = $request->query->get('page', 1);
        $perPage = 10;

        $medias = $em->getRepository(Media::class)->index($page, $perPage, $search);
        $pagination = new Paginator($page, $perPage, count($medias));

        return $this->render('admin/media/list.html.twig', [
            'medias' => $medias,
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("/admin/media/{id}/edit", name="admin_media_edit")
     */
    public function edit(Media $media, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(MediaFormType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($media);
            $em->flush();
            $this->addFlash('success', "Le média a bien été modifié !");
            return $this->redirectToRoute('admin_media');
        }

        return $this->render('admin/media/edit.html.twig', [
            'form' => $form->createView(),
            'media' => $media,
        ]);
    }

    /**
     * @Route("/admin/media/create", name="admin_media_create")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $media = new Media();
        $form = $this->createForm(MediaFormType::class, $media);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($media);
            $em->flush();
            $this->addFlash('success', "Le média a bien été crée !");
            return $this->redirectToRoute('admin_media');
        }

        return $this->render('admin/media/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/media/{id}/delete", name="admin_media_delete")
     */
    public function delete(Media $media): Response
    {
        $questions = $media->getQuestions();

        if ($questions->isEmpty()) {
            return $this->redirectToRoute('admin_media_delete_confirmed', ['id' => $media->getId()]);
        } else {
            return $this->render('admin/media/delete.html.twig', [
              'media' => $media,
              'questions' => $questions,
          ]);
        }
    }

    /**
     * @Route("/admin/media/{id}/delete/confirmed", name="admin_media_delete_confirmed")
     */
    public function deleteConfirmed(Media $media, EntityManagerInterface $em): Response
    {
        $em->remove($media);
        $em->flush();

        $this->addFlash('success', "Le média a été supprimé...");

        return $this->redirectToRoute('admin_media');
    }
}
