<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Form\Type\ClientType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ClientController extends Controller
{
    /**
     * @Route("/clients", name="clients_list")
     * @Method({"GET"})
     * @param EntityManager $em
     * @return Response
     */
    public function indexAction(EntityManager $em)
    {
        $clients = $em->getRepository('AppBundle:Client')
            ->findAll();

        return $this->render('AppBundle:Client:list.html.twig', [
            'clients' => $clients
        ]);
    }

    /**
     * @Route("/clients/add", name="clients_add")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param EntityManager $em
     * @return RedirectResponse|Response
     */
    public function addAction(Request $request, EntityManager $em)
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('clients_list');
        }

        return $this->render('AppBundle:Client:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/clients/edit/{id}", name="clients_edit")
     * @Method({"GET", "POST"})
     * @param $id
     * @param Request $request
     * @param EntityManager $em
     * @return RedirectResponse|Response
     */
    public function editAction($id, Request $request, EntityManager $em)
    {
        $client = $em
            ->getRepository('AppBundle:Client')
            ->find($id);

        if (!$client) {
            throw new NotFoundHttpException('Client not found');
        }

        $form = $this->createForm(ClientType::class, $client);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('clients_list');
        }

        return $this->render('AppBundle:Client:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/clients/view/{id}", name="clients_view")
     * @Method({"GET"})
     * @param $id
     * @param EntityManager $em
     * @return Response
     */
    public function viewAction($id, EntityManager $em)
    {
        $client = $em
            ->getRepository('AppBundle:Client')
            ->find($id);

        if (!$client) {
            throw new NotFoundHttpException('Client not found');
        }

        return $this->render('AppBundle:Client:view.html.twig', array(
            'client' => $client
        ));
    }
}
