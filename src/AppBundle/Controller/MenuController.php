<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenuController extends Controller {
    /**
     * @Route("/", name="menu")
     */
    public function renderTemplateAction() {
        return $this->render('menu.html.twig');
    }
}