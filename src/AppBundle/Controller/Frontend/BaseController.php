<?php

namespace AppBundle\Controller\Frontend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BaseController extends Controller {

    /**
     * Route to log out the user
     *
     * @todo Change this based on your authentication/authorization scheme
     *
     * @Route("/logout", name="logout")
     */
    public function logoutAction() {
        // Put a redirection here for logout
        return $this->redirect("/");
    }
}