<?php
/**
 * Provides routes to download or preview uploaded packing slips
 */

namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class PackingSlipController extends Controller
{
    /**
     * Route to download packing slips
     *
     * @param string $date Date of when the Package was received (also folder name)
     * @param string $trackingNumber Tracking number of Package
     * @param string $filename Filename of file the requested
     *
     * @return Response File to be downloaded with the appropriate headers
     *
     * @Route("/download/{date}/{trackingNumber}/{filename}", name="downloadPackingSlip")
     * @Method({"GET"})
     */
    public function downloadPackingSlipAction($date, $trackingNumber, $filename) {
        // Get PHP's file info tool
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        // Set the path of download
        $path = $this->get('kernel')->getRootDir() . '/../upload/' . $date . '/' . $trackingNumber . '/' . $filename;

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
     * Route to preview packing slips (opens in a new tab instead of downloading)
     *
     * @param string $date Date of when the Package was received (also folder name)
     * @param string $trackingNumber Tracking number of Package
     * @param string $filename Filename of file the requested
     *
     * @return Response File to be previewed with the appropriate headers
     *
     * @Route("/preview/{date}/{trackingNumber}/{filename}", name="previewPackingSlip")
     * @Method({"GET"})
     */
    public function previewPackingSlipAction($date, $trackingNumber, $filename) {
        // Set the path of download
        $path = $this->get('kernel')->getRootDir() . '/../upload/' . $date . '/' . $trackingNumber . '/' . $filename;

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