<?php

namespace SE\AdminBundle\Editor;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class Editor
{
	protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

	public function deleteEntry($entry, $request)
	{ 

	}
}
