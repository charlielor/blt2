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
     * @Route("/receiver/new", name="newReceiver")
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
                    'object' => NULL
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
            } else {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => '\'' . $nameOfNewReceiver . '\' already exists',
                    'object' => NULL
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
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

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully created \'' . $nameOfNewReceiver . '\'',
                'object' => $newReceiver
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }


    }

    /**
     * @Route("/receiver/{id}/update", name="updateReceiver")
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
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            $newReceiverName = $request->request->get("name");

            // Existing Receiver with the same name
            $existingReceiverGivenName = $receiverRepository->findBy(array('name' => $newReceiverName));

            // If no other Receiver has the same name, then update it
            if (empty($existingReceiverGivenName)) {
                $receiverOldName = $receiver->getName();

                // Get user | anon. is temp for testing
                $user = $this->get('security.token_storage')->getToken()->getUser();

                // Set the current Receiver to its new name
                $receiver->setName($request->get('name'), $user);

                // Updating a Receiver will automatically enable it
                $receiver->enabled(TRUE);

                // Get entity manager
                $em = $this->get('doctrine.orm.entity_manager');

                // Push the updated Receiver to database
                $em->persist($receiver);
                $em->flush();

                // Set up the response
                $results = array(
                    'result' => 'success',
                    'message' => 'Successfully updated ' . $receiverOldName . ' to ' . $receiver->getName(),
                    'object' => $receiver
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));

            } else {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => 'Another Receiver already has update name: ' . $newReceiverName,
                    'object' => NULL
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
            }
        }
    }

    /**
     * @Route("/receiver/{id}/enable", name="enableReceiver")
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
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
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
                'object' => $receiver
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }

    }

    /**
     * @Route("/receiver/{id}/disable", name="disableReceiver")
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
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
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
                'object' => $receiver
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }
    }

    /**
     * @Route("/receiver/{id}/delete", name="deleteReceiver")
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
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
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
                'object' => $receiver
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }
    }

    /**
     * @Route("/receiver/search", name="searchReceiver")
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
            WHERE r.name LIKE :term
            AND r.enabled = :enabled'
        )->setParameters(array(
                'term' => $term.'%',
                'enabled' => 1)
        );

        // Run query and save it
        $receiver = $query->getResult();

        // If $receiver is not null, then set up $results to reflect successful query
        if (!(empty($receiver))) {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . count($receiver) . ' Receiver(s) like \'' . $term . '\'',
                'object' => $receiver
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

    /**
     * @Route("/receiver/all", name="allReceivers")
     * @Method({"GET"})
     */
    public function allReceiversAction() {
        // Get the Receiver repository
        $receiverRepository = $this->getDoctrine()->getRepository("AppBundle:Receiver");

        // Get the enabled Receivers
        $receivers = $receiverRepository->findBy([
            "enabled" => true
        ]);

        if (empty($receivers)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not get receivers',
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully retrieved all enabled Receivers',
                'object' => $receivers
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }
    }
}