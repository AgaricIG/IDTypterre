<?php
namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultAdminController extends AbstractController
{

    /**
    * @Route("/admin", name="admin")
    */
    public function home()
    {

        return $this->render('admin/layout.html.twig', array(

        ));
    }

    /**
    * @Route("/admin/tree/pushNodeChanges", name="admin_tree_change")
    */
    public function pushChanges(Request $request)
    {
        $type = $request->request->get('type');

        if($type == 'question') return $this->forward('App\Controller\Admin\QuestionAdminController::push');
        if($type == 'ucs') return $this->forward('App\Controller\Admin\UcsAdminController::push');
        if($type == 'uts') return $this->forward('App\Controller\Admin\UtsAdminController::push');

        throw new \Exception("Type of node is not reconized");
    }

    /**
    * @Route("/admin/tree/uploadNodeFile", name="admin_tree_upload")
    */
    public function uploadFile(Request $request)
    {
        $type = $request->request->get('type');

        if($type == 'question') return $this->forward('App\Controller\Admin\QuestionAdminController::addimage');
        if($type == 'ucs') return $this->forward('App\Controller\Admin\UcsAdminController::addfiche');
        if($type == 'uts') return $this->forward('App\Controller\Admin\UtsAdminController::addfiche');

        throw new \Exception("Type of node is not reconized");
    }

}