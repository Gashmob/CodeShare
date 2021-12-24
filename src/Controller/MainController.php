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
        $titre = $request->get('title');
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
     * @Route("/{uid}", name="see", defaults={"uid":""})
     * @param string $uid
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function see(string $uid, EntityManagerInterface $em): Response
    {
        $uid = trim($uid);

        $code = false;
        $c = $em->getRepository(Code::class)->findOneBy(['uid' => $uid]);
        if ($c != null) {
            $file = fopen('code/' . $uid . '.code', 'r');

            $code = "";
            while (($line = fread($file, 100))) {
                $code .= $line;
            }
        }

        return $this->render('see.html.twig', [
            'title' => $c == null ? 'Sans Titre' : $c->getName(),
            'code' => $code
        ]);
    }

    /**
     * @Route("/raw/{uid}", name="raw")
     * @param string $uid
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function raw(string $uid, EntityManagerInterface $em): Response
    {
        $uid = trim($uid);

        $code = false;
        if ($em->getRepository(Code::class)->findOneBy(['uid' => $uid]) != null) {
            $file = fopen('code/' . $uid . '.code', 'r');

            $code = "";
            while (($line = fread($file, 100))) {
                $code .= $line;
            }
        }

        return new Response($code, Response::HTTP_OK, [
            'content-type' => 'text/plain'
        ]);
    }
}