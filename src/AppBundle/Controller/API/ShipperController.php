<?php
/**
 * Controls everything related to the Shipper entity from creation, update, search, etc.
 */

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
     * Route to creating a new Shipper
     *
     * @api
     *
     * @param Request $request Symfony global request variable
     *
     * @return JsonResponse Results of the call
     * 
     * @Route("/shippers/new", name="newShipper")
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
                    'object' => []
                );

                return new JsonResponse($results);
            } else {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => '\'' . $nameOfNewShipper . '\' already exists',
                    'object' => []
                );

                return new JsonResponse($results);
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

            // Get the new shipper and return it
            $submittedShipper = $shipperRepository->findBy(["name" => $nameOfNewShipper]);

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully created \'' . $nameOfNewShipper . '\'',
                'object' => json_decode($this->get('serializer')->serialize($submittedShipper, 'json'))
            );

            return new JsonResponse($results);
        }


    }

    /**
     * Route for updating a Shipper
     *
     * @api
     *
     * @param Request $request Symfony global request variable
     * @param string $id Shipper's ID
     *
     * @return JsonResponse Results of the call
     * 
     * @Route("/shippers/{id}/update", name="updateShipper")
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
                'object' => []
            );

            return new JsonResponse($results);
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
                $shipper->setEnabled(TRUE, $user);

                // Get entity manager
                $em = $this->get('doctrine.orm.entity_manager');

                // Push the updated Shipper to database
                $em->persist($shipper);
                $em->flush();

                // Get the new shipper and return it
                $submittedShipper = $shipperRepository->findBy(["name" => $newShipperName]);

                // Set up the response
                $results = array(
                    'result' => 'success',
                    'message' => 'Successfully updated ' . $shipperOldName . ' to ' . $shipper->getName(),
                    'object' => json_decode($this->get('serializer')->serialize($submittedShipper, 'json'))
                );

                return new JsonResponse($results);

            } else {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => 'Another Shipper already has update name: ' . $newShipperName,
                    'object' => []
                );

                return new JsonResponse($results);
            }
        }
    }

    /**
     * Route for enabling a Shipper
     *
     * @api
     *
     * @param string $id Shipper's ID
     *
     * @return JsonResponse Results of the call
     * 
     * @Route("/shippers/{id}/enable", name="enableShipper")
     * @Method({"PUT"})
     */
    public function enableShipperAction($id) {
        // Get the Shipper repository
        $shipperRepository = $this->getDoctrine()->getRepository("AppBundle:Shipper");

        // Get the shipper by id
        $shipper = $shipperRepository->find($id);

        if (empty($shipper)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find shipper given id: ' . $id,
                'object' => []
            );

            return new JsonResponse($results);
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
                'object' => json_decode($this->get('serializer')->serialize($shipper, 'json'))
            );

            return new JsonResponse($results);
        }

    }

    /**
     * Route for disabling a Shipper
     *
     * @api
     *
     * @param string $id Shipper's ID
     *
     * @return JsonResponse Results of the call
     * 
     * @Route("/shippers/{id}/disable", name="disableShipper")
     * @Method({"PUT"})
     */
    public function disableShipperAction($id) {
        // Get the Shipper repository
        $shipperRepository = $this->getDoctrine()->getRepository("AppBundle:Shipper");

        // Get the shipper by id
        $shipper = $shipperRepository->find($id);

        if (empty($shipper)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find shipper given id: ' . $id,
                'object' => []
            );

            return new JsonResponse($results);
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
                'object' => json_decode($this->get('serializer')->serialize($shipper, 'json'))
            );

            return new JsonResponse($results);
        }
    }

    /**
     * Route for deleting a Shipper
     *
     * @api
     *
     * @param string $id Shipper's ID
     *
     * @return JsonResponse Results of the call
     * 
     * @Route("/shippers/{id}/delete", name="deleteShipper")
     * @Method({"DELETE"})
     *
     * @todo Can not delete Shipper: will not cascade into Package table
     */
//    public function deleteShipperAction(Request $request, $id) {
//        // Get the Shipper repository
//        $shipperRepository = $this->getDoctrine()->getRepository("AppBundle:Shipper");
//
//        // Get the shipper by id
//        $shipper = $shipperRepository->find($id);
//
//        if (empty($shipper)) {
//            // Set up the response
//            $results = array(
//                'result' => 'error',
//                'message' => 'Can not find shipper given id: ' . $id,
//                'object' => []
//            );
//
//            return new JsonResponse($results);
//        } else {
//            // Get entity manager
//            $em = $this->get('doctrine.orm.entity_manager');
//
//            // Push the updated Shipper to database
//            $em->remove($shipper);
//            $em->flush();
//
//            // Set up the response
//            $results = array(
//                'result' => 'success',
//                'message' => 'Successfully deleted Shipper: ' . $shipper->getName(),
//                'object' => json_decode($this->get('serializer')->serialize($shipper, 'json'))
//            );
//
//            return new JsonResponse($results);
//        }
//    }

    /**
     * Route for searching for a Shipper base on term
     *
     * @api
     *
     * @param Request $request Shipper's name being searched
     *
     * @return JsonResponse Results of the call
     *
     * @Route("/shippers/search", name="searchShipper")
     * @Method({"GET"})
     */
    public function searchShipperAction(Request $request) {
        // Get the term
        $term = $request->query->get('term');

        // Get the entity manager
        $em = $this->get('doctrine.orm.entity_manager');

        // Set up query the database for shippers that is like term
        $query = $em->createQuery(
            'SELECT s FROM AppBundle:Shipper s
            WHERE s.name = :term
            AND s.enabled = :enabled'
        )->setParameters(array(
                'term' => $term,
                'enabled' => 1)
        );

        // Run query and save it
        $shipper = $query->getResult();

        if (empty($shipper)) {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'No Shipper with the name: ' . $term,
                'object' => json_decode($this->get('serializer')->serialize($shipper, 'json'))
            );
        } else {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . $term,
                'object' => json_decode($this->get('serializer')->serialize($shipper, 'json'))
            );
        }

        // Return response as JSON
        return new JsonResponse($results);
    }

    /**
     * Route for searching for a Shipper like term
     *
     * @api
     *
     * @param Request $request Term to use for search
     *
     * @return JsonResponse Results of the call
     * 
     * @Route("/shippers/like", name="likeShipper")
     * @Method({"GET"})
     */
    public function likeShipperAction(Request $request) {
        // Get the term
        $term = $request->query->get('term');

        // Get the entity manager
        $em = $this->get('doctrine.orm.entity_manager');

        // Set up query the database for shippers that is like term
        $query = $em->createQuery(
            'SELECT s FROM AppBundle:Shipper s
            WHERE s.name LIKE :term'
        )->setParameter('term', $term.'%');

        // Run query and save it
        $shipper = $query->getResult();

        if (empty($shipper)) {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'No Shipper(s) like \'' . $term . '\'',
                'object' => json_decode($this->get('serializer')->serialize($shipper, 'json'))
            );
        } else {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . count($shipper) . ' Shipper(s) like \'' . $term . '\'',
                'object' => json_decode($this->get('serializer')->serialize($shipper, 'json'))
            );
        }


        // Return response as JSON
        return new JsonResponse($results);
    }

    /**
     * Route to get all shippers
     *
     * @api
     *
     * @return JsonResponse Results of the call
     *
     * @Route("/shippers", name="shippers")
     * @Method({"GET"})
     */
    public function allShippersAction() {
        // Get the Shipper repository
        $shipperRepository = $this->getDoctrine()->getRepository("AppBundle:Shipper");

        // Get the enabled Shippers
        $shippers = $shipperRepository->findAll();

        // Set up the response
        $results = array(
            'result' => 'success',
            'message' => 'Successfully retrieved all Shippers',
            'object' => json_decode($this->get('serializer')->serialize($shippers, 'json'))
        );

        return new JsonResponse($results);
    }

    /**
     * Route to display Shipper's information
     *
     * @api
     *
     * @param string $id Shipper's ID
     *
     * @return Response Render twig template with Shipper Information
     * 
     * @Route("/shippers/{id}", name="shipper")
     * @Method({"GET"})
     */
    public function shipperAction($id) {
        // Get the Shipper repository
        $shipperRepository = $this->getDoctrine()->getRepository("AppBundle:Shipper");

        // Get the enabled Shippers
        $shipper = $shipperRepository->find($id);

        return $this->render('entity.html.twig', [
            "type" => "shipper",
            "entity" => $shipper
        ]);
    }
}