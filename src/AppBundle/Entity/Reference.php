<?php

namespace AppBundle\Entity;

/**
 * Invoice
 */
class Reference
{
    /**
     * @var int
     */
    private $numerous;

    /**
     * @var string
     */
    private $reference;

    /**
     * Set numerous
     *
     * @param integer $numerous
     *
     * @return Reference
     */
    public function setNumerous($numerous)
    {
        $this->numerous = $numerous;

        return $this;
    }

    /**
     * Get numerous
     *
     * @return int
     */
    public function getNumerous()
    {
        return $this->numerous;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return Reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }
}
