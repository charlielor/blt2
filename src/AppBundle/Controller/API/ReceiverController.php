<?php


namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Receiver;

class ReceiverController extends Controller
{
    /**
     * @Route("/receivers/new", name="newReceiver")
     * @Method({"POST"})
     */
    public function newReceiverAction(Request $request) {
        // Name of new Receiver
        $nameOfNewReceiver = $request->request->get("name");

        // Delivery Room of new Receiver
        $deliveryRoomOfNewReceiver = $request->request->get("deliveryRoom");

        // Get the Receiver repository
        $receiverRepository = $this->getDoctrine()->getRepository("AppBundle:Receiver");

        // Does the new Receiver's name already exists in database
        $existingReceiverGivenName = $receiverRepository->findBy(array('name' => $nameOfNewReceiver));

        // If the query is not empty, a Receiver with the given name already exists
        if (!empty($existingReceiverGivenName)) {
            // If it is disabled, return response letting the user know the Receiver is disabled
            // else return saying that the Receiver already exists
            if ($existingReceiverGivenName[0]->getEnabled() == false) {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => '\'' . $nameOfNewReceiver . '\' already exists; disabled',
                    'object' => []
                );

                return new JsonResponse($results);
            } else {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => '\'' . $nameOfNewReceiver . '\' already exists',
                    'object' => []
                );

                return new JsonResponse($results);
            }
        } else { // Create a new Receiver

            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Create a new Receiver entity and set its properties
            $newReceiver = new Receiver($nameOfNewReceiver, $deliveryRoomOfNewReceiver, $user);

            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the new Receiver to database
            $em->persist($newReceiver);
            $em->flush();

            // Get new Receiver
            $submittedReceiver = $receiverRepository->findBy(array('name' => $nameOfNewReceiver));

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully created \'' . $nameOfNewReceiver . '\'',
                'object' => json_decode($this->get('serializer')->serialize($submittedReceiver, 'json'))
            );

            return new JsonResponse($results);
        }


    }

    /**
     * @Route("/receivers/{id}/update", name="updateReceiver")
     * @Method({"PUT"})
     */
    public function updateReceiverAction(Request $request, $id) {
        // Get the Receiver repository
        $receiverRepository = $this->getDoctrine()->getRepository("AppBundle:Receiver");

        // Get the receiver by id
        $receiver = $receiverRepository->find($id);

        if (empty($receiver)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find receiver given id: ' . $id,
                'object' => []
            );

            return new JsonResponse($results);
        } else {
            $receiverName = $request->request->get("name");

            // Existing Receiver with the same name
            $existingReceiverGivenName = $receiverRepository->findBy(array('name' => $receiverName));

            // If no other Receiver has the same name, then update it
            if (empty($existingReceiverGivenName)) {
                $receiverOldName = $receiver->getName();

                // Get user | anon. is temp for testing
                $user = $this->get('security.token_storage')->getToken()->getUser();

                if (!empty($request->get('name'))) {
                    // Set the current Receiver to its new name
                    $receiver->setName($request->get('name'), $user);
                }

                if (!empty($request->get('deliveryRoom'))) {
                    // Set the current Receiver's delivery room to its new name
                    $receiver->setDeliveryRoom($request->get('deliveryRoom'), $user);
                }

                // Updating a Receiver will automatically enable it
                $receiver->setEnabled(TRUE, $user);

                // Get entity manager
                $em = $this->get('doctrine.orm.entity_manager');

                // Push the updated Receiver to database
                $em->persist($receiver);
                $em->flush();

                $updatedReceiver = $receiverRepository->findBy(array('name' => $receiver->getName()));

                // Set up the response
                $results = array(
                    'result' => 'success',
                    'message' => 'Successfully updated ' . $receiverOldName,
                    'object' => json_decode($this->get('serializer')->serialize($updatedReceiver, 'json'))
                );

                return new JsonResponse($results);

            } else {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => 'Another Receiver already has update name: ' . $receiverName,
                    'object' => []
                );

                return new JsonResponse($results);
            }
        }
    }

    /**
     * @Route("/receivers/{id}/enable", name="enableReceiver")
     * @Method({"PUT"})
     */
    public function enableReceiverAction(Request $request, $id) {
        // Get the Receiver repository
        $receiverRepository = $this->getDoctrine()->getRepository("AppBundle:Receiver");

        // Get the receiver by id
        $receiver = $receiverRepository->find($id);

        if (empty($receiver)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find receiver given id: ' . $id,
                'object' => []
            );

            return new JsonResponse($results);
        } else {
            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Updating a Receiver will automatically enable it
            $receiver->setEnabled(TRUE, $user);

            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the updated Receiver to database
            $em->persist($receiver);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully enabled ' . $receiver->getName(),
                'object' => json_decode($this->get('serializer')->serialize($receiver, 'json'))
            );

            return new JsonResponse($results);
        }

    }

    /**
     * @Route("/receivers/{id}/disable", name="disableReceiver")
     * @Method({"PUT"})
     */
    public function disableReceiverAction(Request $request, $id) {
        // Get the Receiver repository
        $receiverRepository = $this->getDoctrine()->getRepository("AppBundle:Receiver");

        // Get the receiver by id
        $receiver = $receiverRepository->find($id);

        if (empty($receiver)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find receiver given id: ' . $id,
                'object' => []
            );

            return new JsonResponse($results);
        } else {
            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Updating a Receiver will automatically enable it
            $receiver->setEnabled(FALSE, $user);

            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the updated Receiver to database
            $em->persist($receiver);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully disabled ' . $receiver->getName(),
                'object' => json_decode($this->get('serializer')->serialize($receiver, 'json'))
            );

            return new JsonResponse($results);
        }
    }

    /**
     * @Route("/receivers/{id}/delete", name="deleteReceiver")
     * @Method({"DELETE"})
     *
     * TODO: Can not delete Receiver: will not cascade into Package table
     */
    public function deleteReceiverAction(Request $request, $id) {
        // Get the Receiver repository
        $receiverRepository = $this->getDoctrine()->getRepository("AppBundle:Receiver");

        // Get the receiver by id
        $receiver = $receiverRepository->find($id);

        if (empty($receiver)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find receiver given id: ' . $id,
                'object' => []
            );

            return new JsonResponse($results);
        } else {
            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the updated Receiver to database
            $em->remove($receiver);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully deleted Receiver: ' . $receiver->getName(),
                'object' => json_decode($this->get('serializer')->serialize($receiver, 'json'))
            );

            return new JsonResponse($results);
        }
    }

    /**
     * @Route("/receivers/search", name="searchReceiver")
     * @Method({"GET"})
     */
    public function searchReceiverAction(Request $request) {
        // Get the term
        $term = $request->query->get('term');

        // Get the entity manager
        $em = $this->get('doctrine.orm.entity_manager');

        // Set up query the database for receivers that is like terms
        $query = $em->createQuery(
            'SELECT r FROM AppBundle:Receiver r
            WHERE r.name = :term
            AND r.enabled = :enabled'
        )->setParameters(array(
                'term' => $term,
                'enabled' => 1)
        );

        // Run query and save it
        $receiver = $query->getResult();

        // Set up response
        $results = array(
            'result' => 'success',
            'message' => 'Retrieved ' . count($receiver) . ' Receiver',
            'object' => json_decode($this->get('serializer')->serialize($receiver, 'json'))
        );

        // Return response as JSON
        return new JsonResponse($results);
    }

    /**
     * @Route("/receivers/like", name="likeReceiver")
     * @Method({"GET"})
     */
    public function likeReceiverAction(Request $request) {
        // Get the term
        $term = $request->query->get('term');

        // Get the entity manager
        $em = $this->get('doctrine.orm.entity_manager');

        // Set up query the database for receivers that is like terms
        $query = $em->createQuery(
            'SELECT r FROM AppBundle:Receiver r
            WHERE r.name LIKE :term
            AND r.enabled = :enabled'
        )->setParameters(array(
                'term' => $term.'%',
                'enabled' => 1)
        );

        // Run query and save it
        $receiver = $query->getResult();

        // Set up response
        $results = array(
            'result' => 'success',
            'message' => 'Retrieved ' . count($receiver) . ' Receiver(s) like \'' . $term . '\'',
            'object' => json_decode($this->get('serializer')->serialize($receiver, 'json'))
        );

        // Return response as JSON
        return new JsonResponse($results);
    }

    /**
     * @Route("/receivers", name="receivers")
     * @Method({"GET"})
     */
    public function allReceiversAction() {
        // Get the Receiver repository
        $receiverRepository = $this->getDoctrine()->getRepository("AppBundle:Receiver");

        // Get the enabled Receivers
        $receivers = $receiverRepository->findBy([
            "enabled" => 1
        ]);

        // Set up the response
        $results = array(
            'result' => 'success',
            'message' => 'Successfully retrieved all enabled Receivers',
            'object' => json_decode($this->get('serializer')->serialize($receivers, 'json'))
        );

        return new JsonResponse($results);
    }

    /**
     * @Route("/receivers/packages", name="receiverPackages")
     * @Method({"GET"})
     */
    public function receiverPackagesAction(Request $request) {
        if (empty($request->query->get('name'))) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Error in retrieving packages',
                'object' => []
            );

            return new JsonResponse($results);
        } else {
            $name = $request->query->get('name');

            // If receiver is disabled or does not exist, return error
            // Get the Receiver repository
            $receiverRepository = $this->getDoctrine()->getRepository("AppBundle:Receiver");

            $receiver = $receiverRepository->findBy([
                "name" => $name
            ]);

            if (empty($receiver)) {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => 'No such receiver -> ' . $name ,
                    'object' => []
                );

                return new JsonResponse($results);
            } else if (!($receiver[0]->getEnabled())) {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => $name . ' is disabled' ,
                    'object' => []
                );

                return new JsonResponse($results);
            }

            $em = $this->get('doctrine.orm.entity_manager');

            $query = $em->createQuery(
                'SELECT p FROM AppBundle:Package p, AppBundle:Receiver r
                                     WHERE r.name = :name
                                     AND r.id = p.receiver
                                     AND p.delivered = false
                                     AND p.pickedUp = false'
            )->setParameter("name", $name);

            $packages = $query->getResult();

            if (empty($packages)) {
                // Set up the response
                $results = array(
                    'result' => 'success',
                    'message' => 'No packages for ' . $name ,
                    'object' => json_decode($this->get('serializer')->serialize($packages, 'json'))
                );

                return new JsonResponse($results);
            } else {
                // Set up the response
                $results = array(
                    'result' => 'success',
                    'message' => 'Successfully retrieved ' . count($packages) . ' Package(s)' ,
                    'object' => json_decode($this->get('serializer')->serialize($packages, 'json'))
                );

                return new JsonResponse($results);
            }
        }
    }

    /**
     * @Route("/receivers/{id}", name="receiver")
     * @Method({"GET"})
     */
    public function receiverAction($id) {
        // Get the Receiver repository
        $receiverRepository = $this->getDoctrine()->getRepository("AppBundle:Receiver");

        $receiver = $receiverRepository->find($id);

        return $this->render('entity.html.twig', [
            "type" => "receiver",
            "entity" => $receiver
        ]);
    }
}