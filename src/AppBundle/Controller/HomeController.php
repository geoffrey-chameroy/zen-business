<?php

namespace AppBundle\Controller;

use AppBundle\Service\Reporting;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Method({"GET"})
     * @param Reporting $reporting
     * @return Response
     */
    public function indexAction(Reporting $reporting)
    {
        $sales = $reporting->getSalesData();

        return $this->render('AppBundle:Home:index.html.twig', [
            'sales' => $sales
        ]);
    }
}
