<?php
/**
 * Provides routes to search users within the Package table in the database since they don't have their own table
 */

namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller {

    /**
     * Route to get all users in database (no user table or entity)
     *
     * @api
     *
     * @return JsonResponse Results of the call
     *
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
     * Route to search for a user in database base on term
     *
     * @api
     *
     * @param Request $request Symfony global request variable
     *
     * @return JsonResponse Results of the call
     *
     * @Route("/users/search", name="searchUser")
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

        if (empty($users)) {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'Retrieved User with the name: ' . $term,
                'object' => json_decode($this->get('serializer')->serialize($users, 'json'))
            );
        } else {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . $term,
                'object' => json_decode($this->get('serializer')->serialize($users, 'json'))
            );
        }

        return new JsonResponse($results);
    }

    /**
     * Route to serach for a user in database like term
     *
     * @api
     *
     * @param Request $request Symfony global request variable
     *
     * @return JsonResponse Results of the call
     *
     * @Route("/users/like", name="likeUser")
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

        if (empty($users)) {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'No User(s) like \'' . $term . '\'',
                'object' => json_decode($this->get('serializer')->serialize($users, 'json'))
            );
        } else {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . count($users) . ' User(s) like \'' . $term . '\'',
                'object' => json_decode($this->get('serializer')->serialize($users, 'json'))
            );
        }

        return new JsonResponse($results);
    }
}