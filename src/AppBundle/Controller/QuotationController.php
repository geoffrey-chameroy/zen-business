<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Quotation;
use AppBundle\Form\Type\QuotationType;
use AppBundle\Service\ReferenceGenerator;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class QuotationController extends Controller
{
    /**
     * @Route("/quotations", name="quotations_list")
     * @Method({"GET"})
     */
    public function indexAction()
    {
        $quotations = $this->getDoctrine()->getRepository('AppBundle:Quotation')
            ->findAll();

        return $this->render('AppBundle:Quotation:list.html.twig', [
            'quotations' => $quotations
        ]);
    }

    /**
     * @Route("/quotations/add", name="quotations_add")
     * @Method({"GET", "POST"})
     * @param ReferenceGenerator $referenceGenerator
     * @param Request $request
     * @param EntityManager $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(ReferenceGenerator $referenceGenerator, Request $request, EntityManager $em)
    {
        $quotation = new Quotation();
        $form = $this->createForm(QuotationType::class, $quotation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reference = $referenceGenerator->getNextQuotationNumerous($quotation->getDate());
            $quotation->setNumerous($reference->getNumerous());
            $quotation->setReference($reference->getReference());

            $em->persist($quotation);
            $em->flush();

            return $this->redirectToRoute('quotations_list');
        }

        return $this->render('AppBundle:Quotation:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route(
     *     "/office/quotation/{reference}.{_format}", name="quotations_view",
     *     defaults={"_format": "html"},
     *     requirements={
     *          "_format": "pdf"
     *     }
     * )
     * @param $reference
     * @param $_format
     * @return BinaryFileResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function quotationViewAction($reference, $_format)
    {
        $quotation = $this->getDoctrine()->getManager()
            ->getRepository('AppBundle:Quotation')
            ->findOneByReference($reference);

        if (!$quotation) {
            throw new NotFoundHttpException('Quotation not found');
        }

        switch ($_format) {
            default:
                return $this->render('AppBundle::Quotation/view.html.twig', array(
                    'quotation' => $quotation
                ));
            case 'pdf':
                $html = $this->renderView('AppBundle::Quotation/view_pdf.html.twig', array(
                    'quotation' => $quotation
                ));

                return new Response(
                    $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                    200, [
                        'Content-Type' => 'mime/type',
                        'Content-Disposition' => 'filename="' . $quotation->getReference() . '.pdf"'
                    ]
                );
        }
    }
}
