<?php


namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ViewController extends Controller
{
    /**
     * @Route("/view", name="view")
     */
    public function renderTemplateAction() {
        return $this->render('view.html.twig');
    }
}