<?php

namespace App\Controller\Admin;

use App\Entity\Ucs;
use App\Entity\Uts;
use App\Tool\Paginator;
use App\Entity\Question;
use App\Entity\Application;
use App\Form\Admin\UcsFormType;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UcsAdminController extends AbstractController
{
    /**
     * @Route("/admin/ucs", name="admin_ucs")
     */
    public function ucs(Request $request, EntityManagerInterface $em): Response
    {
        $search = $request->query->get('search', "");

        $ucs = $em->getRepository(Ucs::class)->index($search);
        $withGeom = array_filter($ucs, function($u) { return isset($u['geom']); });

        return $this->render('admin/ucs/list.html.twig', [
            'ucs' => $ucs,
            'withGeom' => $withGeom
        ]);
    }

    /**
    * @Route("/admin/ucs/map", name="admin_ucs_map")
    */
    public function home(EntityManagerInterface $em)
    {
        $repo = $em->getRepository(Ucs::class);

        $ucs = $repo->findAll();

        //foreach ($ucs as $uc) {
        //  $uc->geojson = $repo->getGeojson($uc);
        //}

        return $this->render('admin/ucs/map.html.twig', array(
            'ucs' => $ucs,
        ));
    }

    /**
     * @Route("/admin/ucs/{id}/edit", name="admin_ucs_edit")
     */
    public function edit(Ucs $ucs, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UcsFormType::class, $ucs);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ucs);
            $em->flush();
            $this->addFlash('success','La ressource a bien été modifiée!');
            return $this->redirectToRoute('admin_ucs');
        }

        return $this->render('admin/ucs/edit.html.twig', [
            'form' => $form->createView(),
            'ucs' => $ucs,
        ]);
    }

    /**
     * @Route("/admin/ucs/create", name="admin_ucs_create")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $ucs = new Ucs();
        $form = $this->createForm(UcsFormType::class, $ucs);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($ucs);
            $em->flush();
            $this->addFlash('success','La ressource a bien été crée!');
            return $this->redirectToRoute('admin_ucs');
        }

        return $this->render('admin/ucs/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/ucs/{id}/delete", name="admin_ucs_delete")
     */
    public function delete(Ucs $ucs): Response
    {
        $tree = $ucs->getTree();
        if (empty($tree)) {
            return $this->redirectToRoute('admin_ucs_delete_confirmed', ['id' => $ucs->getId()]);
        } else {
            return $this->render('admin/ucs/delete.html.twig', [
              'ucs' => $ucs,
            ]);
        }
    }

    /**
     * @Route("/admin/ucs/{id}/delete/confirmed", name="admin_ucs_delete_confirmed")
     */
    public function deleteConfirmed(Ucs $ucs, EntityManagerInterface $em): Response
    {
        $em->remove($ucs);
        $em->flush();

        $this->addFlash('success', "La ressource a été supprimé !");
        return $this->redirectToRoute('admin_ucs');
    }
    /**
     * @Route("/admin/ucs/push", name="admin_ucs_push")
     */
    public function push(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        $name = $request->request->get('libelle');
        if(!$name || $name == '') return new JsonResponse([]);

        $existant = $em->getRepository(Ucs::class)->findOneByName($name);
        if($existant) {
            return new JsonResponse($serializer->normalize($existant, 'json'));
        }
        else {
            $uts = new Ucs();
            $uts->setName($name);
            $em->persist($uts);
            $em->flush();
            return new JsonResponse($serializer->normalize($uts, 'json'));
        }

    }

    /**
     * @Route("/admin/ucs/{id}/maketree", name="admin_ucs_maketree")
     */
    public function maketree(Ucs $ucs, Request $request, SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        $uts = $em->getRepository(Uts::class)->findBy([], ['id' => 'ASC']);
        $questions = $em->getRepository(Question::class)->findBy([], ['libelle' => 'ASC']);

        return $this->render('admin/ucs/settree.html.twig', [
            'ucs' => $ucs,
            'list_questions' => $serializer->normalize($questions, 'json', ['groups' => 'maker']),
            'list_uts' => $serializer->normalize($uts),
        ]);
    }

    /**
     * @Route("/admin/ucs/{id}/settree", name="admin_ucs_settree")
     */
    public function settree(Ucs $ucs, Request $request, EntityManagerInterface $em): Response
    {
        $content = $request->getContent();
        $json = json_decode($content, true);
        if($json == null) throw new \Exception('Tree is null');
        $ucs->setTree($json);
        $em->flush();

        return new JsonResponse(['success' => 'success']);
    }

    /**
     * @Route("/admin/ucs/{id}/viewtree", name="admin_ucs_viewtree")
     */
    public function viewtree(Ucs $ucs, Request $request, EntityManagerInterface $em): Response
    {
        $applications = $em->getRepository(Application::class)->findAll();

         return $this->render('admin/ucs/viewtree.html.twig', [
            'ucs' => $ucs,
            'applications' => $applications
        ]);
    }

}
