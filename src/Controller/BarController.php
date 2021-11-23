<?php

namespace App\Controller;

use App\Entity\Bar;
use App\Form\BarType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BarController extends AbstractController
{
    #[Route('/bar', name: 'bar')]
    public function index(): Response
    {

        $repository = $this->getDoctrine()->getRepository(Bar::class);
        $mappings = $this->getDoctrine()->getManager()->getClassMetadata(Bar::class);
        $barColumnsNames = $mappings->getFieldNames();
        $bars = $repository->findAll();

        return $this->render('bar/index.html.twig', [
            'controller_name' => 'BarController',
            'bars' => $bars,
            'fields' => $barColumnsNames,
        ]);
    }

    #[Route('/bar/{id}', name: 'bar_id', requirements: ['id' => '\d+'])]
    public function viewId($id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Bar::class);
        $bar = $repository->find($id);

        return $this->render('bar/bar.html.twig', [
            'controller_name' => 'BarController',
            'bar' => $bar,
        ]);
    }

    #[Route('/bar/create', name: 'bar_create')]
    public function create(Request $request): Response
    {
        $bar = new Bar();
        $form = $this->createForm(BarType::class, $bar);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($bar);
            $em->flush();

            return $this->redirectToRoute('bar_id', [
                'id' => $bar->getId()
            ]);
        }

        return $this->render('bar/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
