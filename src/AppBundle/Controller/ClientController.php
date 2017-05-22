<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

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
}
