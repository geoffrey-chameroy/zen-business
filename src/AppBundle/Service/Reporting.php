<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;

class Reporting
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getSalesData()
    {
        $year = date('Y');
        $data = [];

        for ($i = 0; $i < 3; $i++) {
            $data[$i]['label'] = $year - 2 + $i;
            $data[$i]['data'] = $this->_getSalesData($year - 2 + $i);
        }

        return $data;
    }

    private function _getSalesData($year)
    {
        $data = [];
        $invoices = $this->em->getRepository('AppBundle:Invoice')
            ->findByYear($year);

        $m = null;
        $i = -1;
        foreach ($invoices as $invoice) {
            $month = (int)$invoice->getDate()->format('n');
            if ($m != $month) {
                $i++;
                $data[$i][0] = $month;
                $data[$i][1] = 0;
            }
            $data[$i][1] += $invoice->getAmount();
            $m = $month;
        }

        return $data;
    }
}
