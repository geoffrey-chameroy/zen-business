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
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @Route(
     *     "/office/invoice/{reference}.{_format}", name="office_invoice_view",
     *     defaults={"_format": "html"},
     *     requirements={
     *          "_format": "html|pdf"
     *     }
     * )
     * @param $reference
     * @param $_format
     * @return BinaryFileResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function invoiceViewAction($reference, $_format)
    {
        $invoice = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Invoice')
            ->findOneByReference($reference);

        switch ($_format) {
            default:
            case 'html':
                return $this->render('AppBundle::Invoice/view.html.twig', array(
                    'invoice' => $invoice
                ));
            case 'pdf':
                $html = $this->renderView('AppBundle::Invoice/view_pdf.html.twig', array(
                    'invoice' => $invoice
                ));

                return new Response(
                    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                    200,
                    array(
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'filename="' . $invoice->getReference() . '.pdf"'
                    )
                );
        }
    }
}
