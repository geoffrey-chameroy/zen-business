<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class InvoiceController extends Controller
{
    /**
     * @Route("/invoices", name="invoices_list")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        $invoices = $this->getDoctrine()->getRepository('AppBundle:Invoice')
            ->findAll();

        return $this->render('AppBundle:Invoice:list.html.twig', [
            'invoices' => $invoices
        ]);
    }
}
