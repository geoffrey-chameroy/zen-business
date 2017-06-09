<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Invoice;
use AppBundle\Form\Type\InvoiceType;
use AppBundle\Service\ReferenceGenerator;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

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

    /**
     * @Route("/invoices/add", name="invoices_add")
     * @Method({"GET", "POST"})
     * @param ReferenceGenerator $referenceGenerator
     * @param Request $request
     * @param EntityManager $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(ReferenceGenerator $referenceGenerator, Request $request, EntityManager $em)
    {
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reference = $referenceGenerator->getNextInvoiceNumerous($invoice->getDate());
            $invoice->setNumerous($reference->getNumerous());
            $invoice->setReference($reference->getReference());

            $em->persist($invoice);
            $em->flush();

            return $this->redirectToRoute('invoices_list');
        }

        return $this->render('AppBundle:Invoice:add.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
