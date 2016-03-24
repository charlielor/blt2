<?php


namespace AppBundle\Controller\Frontend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ReportingController extends Controller
{
    /**
     * @Route("/reporting", name="reporting")
     */
    public function renderTemplateAction() {
        return $this->render('reporting.html.twig');
    }
}