<?php

namespace App\Controller;

use App\Entity\Bar;
use App\Form\BarType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bar')]
class BarController extends AbstractController
{
    #[Route('', name: 'bars')]
    public function index(): Response
    {

        $repository = $this->getDoctrine()->getRepository(Bar::class);
        $mappings = $this->getDoctrine()->getManager()->getClassMetadata(Bar::class);
        $barColumnsNames = $mappings->getFieldNames();
        $bars = $repository->findBy(array(), array('id' => 'ASC'));

        return $this->render('bar/index.html.twig', [
            'bars' => $bars,
            'fields' => $barColumnsNames,
        ]);
    }

    #[Route('/{id}', name: 'bar_id', requirements: ['id' => '\d+'])]
    public function viewId(Request $request, $id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Bar::class);
        $bar = $repository->find($id);
        $form = $this->createForm(BarType::class, $bar);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($bar);
            $em->flush();

            return $this->redirectToRoute('bars');
        }

        return $this->render('bar/bar.html.twig', [
            'controller_name' => 'BarController',
            'form' => $form->createView(),
        ]);
    }

    #[Route('/create', name: 'bar_create')]
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

        return $this->render('bar/bar.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/delete/{id}/{token}', name: 'bar_delete')]
    public function delete($id, $token): Response
    {

        if ($this->isCsrfTokenValid('delete_bar', $token)) {
            $em = $this->getDoctrine()->getManager();
            $bar = $em->getRepository(Bar::class)->find($id);
            $em->remove($bar);
            $em->flush();
            return $this->redirectToRoute('bars');
        }

        throw new Exception('Invalid token !!!!');
    }
}
