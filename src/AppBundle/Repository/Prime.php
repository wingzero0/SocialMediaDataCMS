<?php

namespace AppBundle\Repository;
use Doctrine\ODM\MongoDB\Query\ReferencePrimer;

trait Prime
{
    /**
     * @param array|\Traversable $documents Documents containing references to prime
     * @param string[] $fields
     */
    public function primeReferences($documents, $fields)
    {
        /* @var \Doctrine\ODM\MongoDB\DocumentManager $dm */
        $dm = $this->getDocumentManager();
        $primer = new ReferencePrimer($dm, $dm->getUnitOfWork());
        foreach ($fields as $field)
        {
            $primer->primeReferences($this->getClassMetadata(), $documents, $field);
        }
    }
}
