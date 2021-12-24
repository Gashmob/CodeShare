<?php

namespace App\Controller;

use App\Entity\Code;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/")
     * @return Response
     */
    public function homepage(): Response
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/submit", methods={"POST"}, name="submit")
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    public function submit(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $titre = $request->get('titre');
        $code = $request->get('code');

        $c = new Code();
        $c->setName($titre);
        $em->persist($c);
        $em->flush();

        $uid = $c->getUid();

        $file = fopen('code/' . $uid . '.code', 'w');
        fwrite($file, $code);

        return $this->json([
            'result' => true,
            'uid' => $uid
        ]);
    }

    /**
     * @Route("/{uid}", name="see")
     * @param string $uid
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function see(string $uid, EntityManagerInterface $em): Response
    {
        $uid = trim($uid);

        return $this->json(['hello']);
    }
}