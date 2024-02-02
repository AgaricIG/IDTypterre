<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Tool\Paginator;
use App\Form\Admin\UserEditFormType;
use App\Form\Admin\UserCreateFormType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserAdminController extends AbstractController
{
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function users(Request $request, EntityManagerInterface $em): Response
    {
        $search = $request->query->get('search', "");
        $page = $request->query->get('page', 1);
        $perPage = 1;

        $users = $em->getRepository(User::class)->index($page, $perPage, $search);

        $pagination = new Paginator($page, $perPage, count($users));

        return $this->render('admin/users/list.html.twig', [
            'users' => $users,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     */
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserEditFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', "L'utilisateur a bien été modifié !");
        }

        return $this->render('admin/users/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/user/create", name="admin_user_create")
     */
    public function create(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        $user = new User();
        $form = $this->createForm(UserCreateFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($hasher->hashPassword($user,$form->get('plainPassword')->getData()));
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', "L'utilisateur a bien été crée !");
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('admin/users/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}/delete", name="admin_user_delete")
     */
    public function delete(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', "L'utilisateur a été supprimé !");

        return $this->redirectToRoute('admin_users');
    }

}
