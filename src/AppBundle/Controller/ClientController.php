<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Form\Type\ClientType;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends Controller
{
    /**
     * @Route("/clients", name="clients_list")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        $clients = $this->getDoctrine()->getRepository('AppBundle:Client')
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
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
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
}
