<?php

namespace AppBundle\Controller\Frontend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller {
    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction() {
        return $this->redirect("https://biochem.wisc.edu");
    }
}