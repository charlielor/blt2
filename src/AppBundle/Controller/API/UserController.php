<?php

namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller {

    /**
     * @Route("/user/all", name="getAllUsers")
     * @Method({"GET"})
     */
    public function getAllUsersAction() {
        $em = $this->get('doctrine.orm.entity_manager');
        $query = $em->createQuery(
            'SELECT DISTINCT p.userWhoReceived FROM AppBundle:Package p
                    ORDER BY p.userWhoReceived ASC'
        );

        $results = $query->getResult();

        $entities = array();

        foreach ($results as $result) {
            $user = [
                'name'=> $result['userWhoReceived']
            ];
            array_push($entities, $user);
        }

        if (!empty($entities)) {
            $results = array(
                'result' => 'success',
                'message' => 'Successfully queried database',
                'object' => $entities
            );
        } else {
            $results = array(
                'result' => 'error',
                'message' => 'Error in querying database',
                'object' => NULL
            );
        }

        return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
    }

    /**
     * @Route("/user/search", name="searchUser")
     * @Method({"GET"})
     */
    public function searchUserAction(Request $request) {
        // Get the term
        $term = $request->get('term');

        // Get the entity manager
        $em = $this->get('doctrine.orm.entity_manager');

        // Set up query the database for users that is like term
        $query = $em->createQuery(
            'SELECT DISTINCT p.userWhoReceived FROM AppBundle:Package p
            WHERE p.userWhoReceived LIKE :term'
        )->setParameter('term', $term.'%');

        // Run query and save it
        $users = $query->getResult();

        // If $receiver is not null, then set up $results to reflect successful query
        if (!(empty($users))) {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . count($users) . ' User(s) like \'' . $term . '\'',
                'object' => $users
            );

        } else {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'Was not able to query database',
                'object' => NULL
            );
        }

        return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
    }
}