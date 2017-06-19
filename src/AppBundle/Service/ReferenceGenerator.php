<?php

namespace AppBundle\Service;

use AppBundle\Entity\Reference;
use Doctrine\ORM\EntityManager;

class ReferenceGenerator
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getNextInvoiceNumerous(\DateTime $date)
    {
        $invoice = $this->em->getRepository('AppBundle:Invoice')
            ->getLastInvoice($date);
        $num = $invoice ? $invoice->getNumerous() + 1 : 1;
        $ref = 'F' . $date->format('ym') . '-' . str_pad($num, '3', '0', STR_PAD_LEFT);

        $reference = new Reference();

        return $reference->setNumerous($num)
            ->setReference($ref);
    }

    public function getNextQuotationNumerous(\DateTime $date)
    {
        $quotation = $this->em->getRepository('AppBundle:Quotation')
            ->getLastQuotation($date);
        $num = $quotation ? $quotation->getNumerous() + 1 : 1;
        $ref = 'D' . $date->format('ym') . '-' . str_pad($num, '3', '0', STR_PAD_LEFT);
        $reference = new Reference();

        return $reference->setNumerous($num)
            ->setReference($ref);
    }
}
