<?php


namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Shipper;

class ShipperController extends Controller
{
    /**
     * @Route("/shipper/new", name="newShipper")
     * @Method({"POST"})
     */
    public function newShipperAction(Request $request) {
        // Name of new Shipper
        $nameOfNewShipper = $request->request->get("name");

        // Get the Shipper repository
        $shipperRepository = $this->getDoctrine()->getRepository("AppBundle:Shipper");

        // Does the new Shipper's name already exists in database
        $existingShipperGivenName = $shipperRepository->findBy(array('name' => $nameOfNewShipper));

        // If the query is not empty, a Shipper with the given name already exists
        if (!empty($existingShipperGivenName)) {
            // If it is disabled, return response letting the user know the Shipper is disabled
            // else return saying that the Shipper already exists
            if ($existingShipperGivenName[0]->getEnabled() == false) {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => '\'' . $nameOfNewShipper . '\' already exists; disabled',
                    'object' => NULL
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
            } else {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => '\'' . $nameOfNewShipper . '\' already exists',
                    'object' => NULL
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
            }
        } else { // Create a new Shipper

            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Create a new Shipper entity and set its properties
            $newShipper = new Shipper($nameOfNewShipper, $user);

            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the new Shipper to database
            $em->persist($newShipper);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully created \'' . $nameOfNewShipper . '\'',
                'object' => $newShipper
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }


    }

    /**
     * @Route("/shipper/{id}/update", name="updateShipper")
     * @Method({"PUT"})
     */
    public function updateShipperAction(Request $request, $id) {
        // Get the Shipper repository
        $shipperRepository = $this->getDoctrine()->getRepository("AppBundle:Shipper");

        // Get the shipper by id
        $shipper = $shipperRepository->find($id);

        if (empty($shipper)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find shipper given id: ' . $id,
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            $newShipperName = $request->request->get("name");

            // Existing Shipper with the same name
            $existingShipperGivenName = $shipperRepository->findBy(array('name' => $newShipperName));

            // If no other Shipper has the same name, then update it
            if (empty($existingShipperGivenName)) {
                $shipperOldName = $shipper->getName();

                // Get user | anon. is temp for testing
                $user = $this->get('security.token_storage')->getToken()->getUser();

                // Set the current Shipper to its new name
                $shipper->setName($request->get('name'), $user);

                // Updating a Shipper will automatically enable it
                $shipper->enabled(TRUE);

                // Get entity manager
                $em = $this->get('doctrine.orm.entity_manager');

                // Push the updated Shipper to database
                $em->persist($shipper);
                $em->flush();

                // Set up the response
                $results = array(
                    'result' => 'success',
                    'message' => 'Successfully updated ' . $shipperOldName . ' to ' . $shipper->getName(),
                    'object' => $shipper
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));

            } else {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => 'Another Shipper already has update name: ' . $newShipperName,
                    'object' => NULL
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
            }
        }
    }

    /**
     * @Route("/shipper/{id}/enable", name="enableShipper")
     * @Method({"PUT"})
     */
    public function enableShipperAction(Request $request, $id) {
        // Get the Shipper repository
        $shipperRepository = $this->getDoctrine()->getRepository("AppBundle:Shipper");

        // Get the shipper by id
        $shipper = $shipperRepository->find($id);

        if (empty($shipper)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find shipper given id: ' . $id,
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Updating a Shipper will automatically enable it
            $shipper->setEnabled(TRUE, $user);

            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the updated Shipper to database
            $em->persist($shipper);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully enabled ' . $shipper->getName(),
                'object' => $shipper
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }

    }

    /**
     * @Route("/shipper/{id}/disable", name="disableShipper")
     * @Method({"PUT"})
     */
    public function disableShipperAction(Request $request, $id) {
        // Get the Shipper repository
        $shipperRepository = $this->getDoctrine()->getRepository("AppBundle:Shipper");

        // Get the shipper by id
        $shipper = $shipperRepository->find($id);

        if (empty($shipper)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find shipper given id: ' . $id,
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Updating a Shipper will automatically enable it
            $shipper->setEnabled(FALSE, $user);

            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the updated Shipper to database
            $em->persist($shipper);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully disabled ' . $shipper->getName(),
                'object' => $shipper
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }
    }

    /**
     * @Route("/shipper/{id}/delete", name="deleteShipper")
     * @Method({"PUT"})
     *
     * TODO: Can not delete Shipper: will not cascade into Package table
     */
    public function deleteShipperAction(Request $request, $id) {
        // Get the Shipper repository
        $shipperRepository = $this->getDoctrine()->getRepository("AppBundle:Shipper");

        // Get the shipper by id
        $shipper = $shipperRepository->find($id);

        if (empty($shipper)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find shipper given id: ' . $id,
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the updated Shipper to database
            $em->remove($shipper);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully deleted Shipper: ' . $shipper->getName(),
                'object' => $shipper
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }
    }

    /**
     * @Route("/shipper/search", name="searchShipper")
     * @Method({"GET"})
     */
    public function searchShipperAction(Request $request) {
        // Get the term
        $term = $request->query->get('term');

        // Get the entity manager
        $em = $this->get('doctrine.orm.entity_manager');

        // Set up query the database for receivers that is like terms
        $query = $em->createQuery(
            'SELECT v FROM AppBundle:Shipper v
            WHERE v.name LIKE :term
            AND v.enabled = :enabled'
        )->setParameters(array(
                'term' => $term.'%',
                'enabled' => 1)
        );

        // Run query and save it
        $shipper = $query->getResult();

        // If $receiver is not null, then set up $results to reflect successful query
        if (!(empty($shipper))) {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . count($shipper) . ' Shipper(s) like \'' . $term . '\'',
                'object' => $shipper
            );

            // Return response as JSON
            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'Was not able to query database',
                'object' => NULL
            );

            // Return response as JSON
            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }
    }
}