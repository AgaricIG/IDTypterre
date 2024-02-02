<?php

namespace App\Controller\Admin;

use App\Entity\Uts;
use App\Tool\Paginator;
use App\Entity\Question;
use App\Form\Admin\UtsFormType;
use Symfony\Component\Finder\Finder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UtsAdminController extends AbstractController
{
    /**
     * @Route("/admin/uts", name="admin_uts")
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $search = $request->query->get('search', "");
        $page = $request->query->get('page', 1);
        $perPage = 20;

        $uts = $em->getRepository(Uts::class)->index($page, $perPage, $search);
        $pagination = new Paginator($page, $perPage, count($uts));

        return $this->render('admin/uts/list.html.twig', [
            'uts' => $uts,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/admin/uts/{id}/edit", name="admin_uts_edit")
     */
    public function edit(Uts $uts, Request $request, EntityManagerInterface $em): Response
    {
        $originalFicheSuppls = new ArrayCollection();
        foreach ($uts->getFicheSuppls() as $fiche) {
            $originalFicheSuppls->add($fiche);
        }

        $form = $this->createForm(UtsFormType::class, $uts);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            foreach ($originalFicheSuppls as $fiche) {
                $fiche->setUts($uts);
                if (false === $uts->getFicheSuppls()->contains($fiche)) {
                    $em->remove($fiche);
                }
            }
            $em->persist($uts);
            $em->flush();
            $this->addFlash('success', 'La ressource a bien été modifiée!');
        }

        return $this->render('admin/uts/edit.html.twig', [
            'form' => $form->createView(),
            'uts' => $uts,
        ]);
    }

    /**
     * @Route("/admin/uts/create", name="admin_uts_create")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $uts = new Uts();
        $form = $this->createForm(UtsFormType::class, $uts);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($uts);
            $em->flush();
            $this->addFlash('success', 'La ressource a bien été crée!');
            return $this->redirectToRoute('admin_uts');
        }

        return $this->render('admin/uts/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/uts/{id}/delete", name="admin_uts_delete")
     */
    public function delete(Uts $uts): Response
    {
        $ucs = $uts->getUcs();
        if ($ucs->isEmpty()) {
            return $this->redirectToRoute('admin_uts_delete_confirmed', ['id' => $uts->getId()]);
        } else {
            return $this->render('admin/uts/delete.html.twig', [
              'uts' => $uts,
              'ucs' => $ucs,
            ]);
        }
    }

    /**
     * @Route("/admin/uts/{id}/delete/confirmed", name="admin_uts_delete_confirmed")
     */
    public function deleteConfirmed(Uts $uts, EntityManagerInterface $em): Response
    {
        $em->remove($uts);
        $em->flush();

        $this->addFlash('success', "La ressource a été supprimé !");
        return $this->redirectToRoute('admin_uts');
    }

    /**
     * @Route("/admin/uts/push", name="admin_uts_push")
     */
    public function push(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        $name = $request->request->get('libelle');
        if (!$name || $name == '') {
            return new JsonResponse([]);
        }

        $existant = $em->getRepository(Uts::class)->findOneByName($name);
        if ($existant) {
            return new JsonResponse($serializer->normalize($existant, 'json'));
        } else {
            $uts = new Uts();
            $uts->setName($name);
            $em->persist($uts);
            // remove unwanted question with the same name
            $q = $em->getRepository(Question::class)->findOneByLibelle($name);
            if ($q) {
                $em->remove($q);
            }

            $em->flush();
            return new JsonResponse($serializer->normalize($uts, 'json'));
        }
    }

    /**
     * @Route("/admin/uts/addfiche", name="admin_uts_addfiche")
     */
    public function addfiche(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        $id = $request->request->get('id');
        $uts = $em->getRepository(Uts::class)->find($id);
        if (!$uts) {
            return new JsonResponse(['error' => "Impossible de trouver l'UTS avec l'id '$id'"]);
        }

        $file = $request->files->get('fiche');
        $folder = $this->getParameter('kernel.project_dir').'/public/images/uts';
        $filename = 'UTT_'.$uts->getId().'.'.$file->getClientOriginalExtension();
        // move file to public folder
        $res = $file->move($folder, $filename);
        $webpath = '/images/uts/'.$filename;
        // save uts
        $uts->setFiche($webpath);
        $em->persist($uts);
        $em->flush();
        return new JsonResponse($serializer->normalize($uts, 'json'));
    }
}
