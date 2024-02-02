<?php
namespace App\Controller\Api;

use App\Entity\Ucs;
use App\Entity\Uts;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class BridgeApiController extends AbstractController
{

    /**
    * @Route("/api/getucs/{id}", name="api_getucs")
    */
    public function getUCS(Ucs $ucs, SerializerInterface $serializer)
    {
        return new JsonResponse([
           'ucs' => [$serializer->normalize($ucs)]
        ]);
    }

    /**
    * @Route("/api/getucsatcoords", name="api_getucs")
    */
    public function getucsatcoords(Request $request, EntityManagerInterface $em, SerializerInterface $serializer)
    {
        $lat = $request->query->get('lat');
        $lng = $request->query->get('lng');
        $srid = $request->query->get('srid');

        if($lat === null || $lng === null) {
            throw new BadRequestException("Missing lat or lng parameters");
        }

        $ucs = $em->getRepository(Ucs::class)->getUcsAtCoords((float) $lat, (float) $lng, $srid);

        return new JsonResponse([
          'lat' => $lat,
          'lng' => $lng,
          'srid' => $srid,
          'ucs' => ($ucs)? [$serializer->normalize($ucs, 'json')] : [],
        ]);
    }

    /**
    * @Route("/api/gettree/{id}", name="api_gettree")
    */
    public function getTree(Ucs $ucs, SerializerInterface $serializer)
    {
        return new JsonResponse($ucs->getTree());
    }

    /**
    * @Route("/api/getquestion/{ucs}/{id}", name="api_getquestion")
    */
    public function getQuestion(Ucs $ucs, $id, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $question = $em->getRepository(Question::class)->find($id);
        $media = $question->getMedia();
        return new JsonResponse(array(
            'no_ucs' => $ucs->getId(),
            'id_question' => $question->getId(),
            'imagelink' => ($media) ? $media->getWebPath() : '',
            'type' => 'rQ',
            'externallink' => '',
        ));
    }

    /**
    * @Route("/api/getuts/{id}", name="api_getuts")
    */
    public function getUts($id, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        $uts = $em->getRepository(Uts::class)->find($id);
        return new JsonResponse(array(
            'no_utx' => $uts->getId(),
            'nom_utx' => $uts->getName(),
            'no_question' => $uts->getId(),
            'ressourcelink' => $uts->getFiche(),
            'externallink' => "",
            'type' => 'rUTS',
            'srid' => "4326",
            'externallink' => '',
            'fiche_suppls' => $serializer->normalize($uts->getFicheSuppls(), 'json', ['groups' => 'api'])
        ));
    }

    /**
    * @Route("/api/getutsbycode/{ucs}/{code}", name="api_getutsbycode")
    */
    public function getUtsByCode(Ucs $ucs, $code, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        /* no real use, it just get a random uts */
        $uts = $em->getRepository(Uts::class)->findAll();
        $uts = $uts[0];

        return new JsonResponse([
           'uts' => $serializer->normalize($uts)
        ]);
    }

    /**
    * @Route("/api/getutsbydefault/{ucs}/{question}", name="api_getutsbydefault")
    */
    public function getUtsByDefault(Ucs $ucs, Question $question, SerializerInterface $serializer, EntityManagerInterface $em)
    {
        /* no real use, it just get a random uts */
        $uts = $em->getRepository(Uts::class)->findAll();
        $uts = $uts[0];

        return new JsonResponse([
           'uts' => $serializer->normalize($uts)
        ]);
    }

    /**
    * @Route("/api/setuts", name="api_setuts")
    */
    public function setUts(SerializerInterface $serializer)
    {
        return new Response('1');
    }


}