<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class MenuController extends Controller {
    /**
     * @Route("/", name="menu")
     */
    public function indexAction() {
        return $this->render('menu.html.twig');
    }
}