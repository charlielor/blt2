<?php

namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class PackingSlipController extends Controller
{
    /**
     * @Route("/download/{date}/{trackingNumber}/{filename}", name="downloadPackingSlip")
     * @Method({"GET"})
     */
    public function downloadPackingSlipAction($date, $trackingNumber, $filename) {
        // Get PHP's file info tool
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        // Set the path of download
        $path = $this->get('kernel')->getRootDir() . '/../uploads/' . $date . '/' . $trackingNumber . '/' . $filename;

        // Set the content
        $content = file_get_contents($path);

        // Create a new response
        $response = new Response();

        // Set the response content type and content disposition
        $response->headers->set('Content-Type', finfo_file($finfo, $path));
        $response->headers->set('Content-Disposition', 'attachment;filename=' . $filename);

        // Set the response content
        $response->setContent($content);

        // Return the response as a download
        return $response;
    }

    /**
     * @Route("/preview/{date}/{trackingNumber}/{filename}", name="previewPackingSlip")
     * @Method({"GET"})
     */
    public function previewPackingSlipAction($date, $trackingNumber, $filename) {
        // Set the path of download
        $path = $this->get('kernel')->getRootDir() . '/../uploads/' . $date . '/' . $trackingNumber . '/' . $filename;

        // Get PHP's file info tool
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        // Set the content
        $content = file_get_contents($path);

        // Create a new response
        $response = new Response();

        // Set the response content type and content disposition
        $response->headers->set('Content-Type', finfo_file($finfo, $path));
        $response->headers->set('Content-Disposition', 'inline;filename=' . $filename);

        // Set the response content
        $response->setContent($content);

        return $response;
    }
}