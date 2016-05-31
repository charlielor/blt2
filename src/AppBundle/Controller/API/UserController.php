<?php

namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller {

    /**
     * @Route("/users", name="users")
     * @Method({"GET"})
     */
    public function getAllUsersAction() {
        $em = $this->get('doctrine.orm.entity_manager');
        $query = $em->createQuery(
            'SELECT DISTINCT p.userWhoReceived as username FROM AppBundle:Package p
                    ORDER BY p.userWhoReceived ASC'
        );

        $results = $query->getResult();

        $results = array(
            'result' => 'success',
            'message' => 'Successfully queried database',
            'object' => json_decode($this->get('serializer')->serialize($results, 'json'))
        );

        return new JsonResponse($results);
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
            'SELECT DISTINCT p.userWhoReceived as username FROM AppBundle:Package p
            WHERE p.userWhoReceived = :term'
        )->setParameter('term', $term);

        // Run query and save it
        $users = $query->getResult();

        // If $receiver is not null, then set up $results to reflect successful query
        if (!(empty($users))) {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . count($users) . ' User',
                'object' => json_decode($this->get('serializer')->serialize($users, 'json'))
            );

        } else {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'Was not able to query database',
                'object' => []
            );
        }

        return new JsonResponse($results);
    }

    /**
     * @Route("/user/like", name="likeUser")
     * @Method({"GET"})
     */
    public function likeUserAction(Request $request) {
        // Get the term
        $term = $request->get('term');

        // Get the entity manager
        $em = $this->get('doctrine.orm.entity_manager');

        // Set up query the database for users that is like term
        $query = $em->createQuery(
            'SELECT DISTINCT p.userWhoReceived as username FROM AppBundle:Package p
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
                'object' => json_decode($this->get('serializer')->serialize($users, 'json'))
            );

        } else {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'Was not able to query database',
                'object' => []
            );
        }

        return new JsonResponse($results);
    }
}