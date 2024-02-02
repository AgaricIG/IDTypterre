<?php

namespace App\Controller\Admin;

use App\Entity\Uts;
use App\Entity\Media;
use App\Tool\Paginator;
use App\Entity\Question;
use App\Form\Admin\QuestionFormType;
use Symfony\Component\Finder\Finder;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class QuestionAdminController extends AbstractController
{
    /**
     * @Route("/admin/question", name="admin_question")
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $search = $request->query->get('search', "");
        $page = $request->query->get('page', 1);
        $perPage = 10;

        $questions = $em->getRepository(Question::class)->index($page, $perPage, $search);
        $pagination = new Paginator($page, $perPage, count($questions));

        return $this->render('admin/question/list.html.twig', [
            'questions' => $questions,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/admin/question/{id}/edit", name="admin_question_edit")
     */
    public function edit(Question $question, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(QuestionFormType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($question->getMedia()->getType() == '') {
                $question->setMedia(null);
            }
            $em->persist($question);
            $em->flush();
            $this->addFlash('success', "La question a bien été modifiée !");
            return $this->redirectToRoute('admin_question');
        }

        return $this->render('admin/question/edit.html.twig', [
            'form' => $form->createView(),
            'question' => $question,
            'medias' => $em->getRepository(Media::class)->findAll()
        ]);
    }

    /**
     * @Route("/admin/question/create", name="admin_question_create")
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $question = new Question();
        $form = $this->createForm(QuestionFormType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($question->getMedia()->getType() == '') {
                $question->setMedia(null);
            }
            $em->persist($question);
            $em->flush();
            $this->addFlash('success', "La question a bien été crée !");
            return $this->redirectToRoute('admin_question');
        }

        return $this->render('admin/question/create.html.twig', [
            'form' => $form->createView(),
            'medias' => $em->getRepository(Media::class)->findAll()
        ]);
    }

    /**
     * @Route("/admin/question/{id}/delete", name="admin_question_delete")
     */
    public function delete(Question $question, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($question);
        $em->flush();

        $this->addFlash('success', "La question a été supprimée !");

        return $this->redirectToRoute('admin_question');
    }

    /**
     * @Route("/admin/question/push", name="admin_question_push")
     */
    public function push(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        $libelle = $request->request->get('libelle');
        if (!$libelle || $libelle == '') {
            return new JsonResponse([]);
        }

        $existant = $em->getRepository(Question::class)->findOneByLibelle($libelle);

        if ($existant) {
            return new JsonResponse($serializer->normalize($existant, 'json', ['groups' => 'maker']));
        } else {
            $question = new Question();
            $question->setLibelle($libelle);
            $em->persist($question);
            // remove misscreated uts with the same name
            $u = $em->getRepository(Uts::class)->findOneByName($libelle);
            if ($u) {
                $em->remove($u);
            }

            $em->flush();
            return new JsonResponse($serializer->normalize($question, 'json', ['groups' => 'maker']));
        }
    }

    /**
     * @Route("/admin/question/addimage", name="admin_question_addimage")
     */
    public function addimage(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        $id = $request->request->get('id');
        if (!is_numeric($id)) {
            return new JsonResponse(['error' => "L'id de la question doit être un entier"]);
        }
        $question = $em->getRepository(Question::class)->find(intval($id));
        if (!$question) {
            return new JsonResponse(['error' => "Cette question n'existe pas"]);
        } else {
            $file = $request->files->get('file');
            $folder = $this->getParameter('kernel.project_dir').'/public/'.Media::DIR;
            $filename = strtolower(trim(preg_replace('/[^A-Za-z0-9-\.]+/', '-', $file->getClientOriginalName())));
            // count files with same name and increment name if > 0
            $finder = new Finder();
            $count = $finder->in($folder)->name($filename)->files()->count();
            if ($count > 0) {
                $filename = str_replace('.', $count.'.', $filename);
            }
            // move file to public folder
            $file->move($folder, $filename);
            $webpath = Media::DIR.'/'.$filename;

            $media = new Media();
            $media->setDescription($request->request->get('description'));
            $media->setType('image');
            $media->setUrl($webpath);
            // save question
            $question->setMedia($media);
            $em->persist($question);
            $em->flush();
            return new JsonResponse($serializer->normalize($question, 'json', ['groups' => 'maker']));
        }
    }
}
