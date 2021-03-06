<?php

namespace AppBundle\Controller\Frontend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MenuController extends Controller {

    /**
     * Route to render the main menu page
     *
     * @Route("/", name="menu")
     */
    public function renderTemplateAction() {
        return $this->render('menu.html.twig');
    }
}