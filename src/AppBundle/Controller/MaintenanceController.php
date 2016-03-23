<?php


namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\Query;
use Doctrine\ORM\Mapping as ORM;


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

    /**
     * When the user clicks on the enable or disable button, enable or disable the entity
     *
     * @Route("/maintenance/switch", name="maintenanceSwitch")
     *
     * @param Request $request - Current Request Stack
     * @return Response - JSON response
     *
     * TODO: Update userLastModified column
     */
    public function switchAction(Request $request) {
        // Extract GET information
        $repositoryInfo = $request->get('repository');
        $id = $request->get('id');
        $action = $request->get('action');

        // Set up response
        $results = array(
            'result' => '',
            'message' => ''
        );

        // If either extracted information is empty, return error
        if (empty($repositoryInfo) || empty($id) || empty($action)) {
            $results['result'] = 'error';
            $results['message'] = 'Error in extracting GET parameters';

            return new Response($this->get('serializer')->serialize($results, 'json'));
        }

        // Set up repository
        $repository = null;

        // If the repositoryInfo says receiver, get the repository for receiver
        // Else if the repositoryInfo says shipper, get the repository for shipper
        // Else if the repositoryInfo says vendor, get the repository for vendor
        // Else return an error
        if ($repositoryInfo == 'receiver') {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Receiver');
        } else if ($repositoryInfo == 'shipper') {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Shipper');
        } else if ($repositoryInfo == 'vendor') {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Vendor');
        } else {
            $results['result'] = 'error';
            $results['message'] = 'Was not able to get repository given GET parameters';

            return new Response($this->get('serializer')->serialize($results, 'json'));
        }

        // If the repository is empty, then return an error saying that it wasn't able to retrieve the repository information
        if (empty($repository)) {
            $results['result'] = 'error';
            $results['message'] = 'Was not able to get repository given repository information';

            return new Response($this->get('serializer')->serialize($results, 'json'));
        }

        // Get the entity given id
        // Both shipper and receiver have the same methods for getting and setting enabled
        $object = $repository->find($id);

        // If the action is to disable an entity, then check to see if it's already disabled
        // If it's already disabled, return error
        // If the action is to enable an entity, then check to see if it's already enabled
        // If it's already enabled, return error
        // If the action is either of the two above, then return error
        if ($action == 'Disable') {
            if ($object->getEnabled() == true) {
                $object->setEnabled(false);

                $results['result'] = 'success';
                $results['message'] = $object->getName() . ' was successfully disabled';
            } else {
                $results['result'] = 'error';
                $results['message'] = $object->getName() . ' was already disabled';

                return new Response($this->get('serializer')->serialize($results, 'json'));
            }
        } else if ($action == 'Enable') {
            if ($object->getEnabled() == false) {
                $object->setEnabled(true);
                $results['result'] = 'success';
                $results['message'] = $object->getName() . ' was successfully enabled';

            } else {
                $results['result'] = 'error';
                $results['message'] = $object->getName() . ' was already enabled';

                return new Response($this->get('serializer')->serialize($results, 'json'));
            }
        } else {
            $results['result'] = 'error';
            $results['message'] = 'Not a valid action';

            return new Response($this->get('serializer')->serialize($results, 'json'));
        }

        // Get the entity manager
        $em = $this->getDoctrine()->getManager();

        // Persist the object into the entity manager
        $em->persist($object);
        // Push object into database
        $em->flush($object);

        // Return results
        return new Response($this->get('serializer')->serialize($results, 'json'));
    }

    /**
     * Search action for searching vendors and tracking numbers.
     *
     * @Route("/maintenance/search", name="maintenanceSearch")
     *
     * @param Request $request - Current Request Stack
     * @return Response - Twig template
     *
     * TODO: Update userLastModified column
     */
    public function searchAction(Request $request) {
        // Get the searched term
        $term = $request->get('term');

        // Get the repository information
        $repositoryInfo = $request->get('repository');

        // Set up response
        $results = array(
            'result' => 'error',
            'message' => 'There was an error in extracting GET parameters',
            'object' => NULL
        );

        if (empty($term) || empty($repositoryInfo)) {
            return new Response($this->get('serializer')->serialize($results, 'json'));
        }

        // Sanitize the term
        $term = filter_var($term, FILTER_SANITIZE_STRING);

        // Get the entity manager
        $em = $this->getDoctrine()->getManager();

        // Set the query to null
        $query = null;

        // Set up the query base on repository information else return error
        if ($repositoryInfo == 'vendor') {
            $query = $em->createQuery(
                'SELECT v FROM AppBundle:Vendor v
                WHERE v.name LIKE :term'
            )->setParameter('term', '%'.$term.'%');
        } else if ($repositoryInfo == 'package') {
            $query = $em->createQuery(
                "SELECT p FROM AppBundle:Package p
                WHERE p.trackingNumber LIKE :term"
            )->setParameter('term', $term.'%');
        } else {
            $results = array(
                'message' => 'There was an error in retrieving information'
            );

            return new Response($this->get('serializer')->serialize($results, 'json'));
        }

        // Get the entities
        $objects = $query->getResult();

        // Set up response
        $results = array(
            'result' => 'success',
            'message' => 'Retrieved ' . count($objects) . ' ' . $repositoryInfo . ' like ' . $term,
            'object' => $objects
        );

        // Return the maintenance results template with the given array
        return new Response($this->get('serializer')->serialize($results, 'json'));

    }
}