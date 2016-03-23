<?php


namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MaintenanceController extends Controller
{
    /**
     * @Route("/maintenance", name="maintenance")
     */
    public function renderTemplateAction() {
        // Get the repositories for receiver and shipper
        $receiverRepository = $this->getDoctrine()->getRepository('AppBundle:Receiver');
        $shipperRepository = $this->getDoctrine()->getRepository('AppBundle:Shipper');

        // Get an array of receivers
        $receivers = $receiverRepository->findAll();

        // Get an array of shippers
        $shippers = $shipperRepository->findAll();

        return $this->render('maintenance.html.twig', array(
            "receivers" => $receivers,
            "shippers" => $shippers
        ));
    }
}