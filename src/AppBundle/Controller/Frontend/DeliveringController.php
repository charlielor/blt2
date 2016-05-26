<?php


namespace AppBundle\Controller\Frontend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use Doctrine\ORM\Mapping as ORM;

class DeliveringController extends Controller
{

    /**
     * @Route("/delivering", name="delivering")
     */
    public function renderTemplateAction() {
        return $this->render('delivering.html.twig');
    }

}