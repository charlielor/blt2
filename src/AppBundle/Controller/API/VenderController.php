<?php


namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Vendor;

class VenderController extends Controller
{
    /**
     * @Route("/vendor/new", name="newVendor")
     * @Method({"POST"})
     */
    public function newVendorAction(Request $request) {
        // Name of new Vendor
        $nameOfNewVendor = $request->request->get("name");

        // Get the Vendor repository
        $vendorRepository = $this->getDoctrine()->getRepository("AppBundle:Vendor");

        // Does the new Vendor's name already exists in database
        $existingVendorGivenName = $vendorRepository->findBy(array('name' => $nameOfNewVendor));

        // If the query is not empty, a Vendor with the given name already exists
        if (!empty($existingVendorGivenName)) {
            // If it is disabled, return response letting the user know the Vendor is disabled
            // else return saying that the Vendor already exists
            if ($existingVendorGivenName[0]->getEnabled() == false) {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => '\'' . $nameOfNewVendor . '\' already exists; disabled',
                    'object' => NULL
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
            } else {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => '\'' . $nameOfNewVendor . '\' already exists',
                    'object' => NULL
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
            }
        } else { // Create a new Vendor

            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Create a new Vendor entity and set its properties
            $newVendor = new Vendor($nameOfNewVendor, $user);

            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the new Vendor to database
            $em->persist($newVendor);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully created \'' . $nameOfNewVendor . '\'',
                'object' => $newVendor
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }


    }

    /**
     * @Route("/vendor/{id}/update", name="updateVendor")
     * @Method({"PUT"})
     */
    public function updateVendorAction(Request $request, $id) {
        // Get the Vendor repository
        $vendorRepository = $this->getDoctrine()->getRepository("AppBundle:Vendor");

        // Get the vendor by id
        $vendor = $vendorRepository->find($id);

        if (empty($vendor)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find vendor given id: ' . $id,
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            $newVendorName = $request->request->get("name");

            // Existing Vendor with the same name
            $existingVendorGivenName = $vendorRepository->findBy(array('name' => $newVendorName));

            // If no other Vendor has the same name, then update it
            if (empty($existingVendorGivenName)) {
                $vendorOldName = $vendor->getName();

                // Get user | anon. is temp for testing
                $user = $this->get('security.token_storage')->getToken()->getUser();

                // Set the current Vendor to its new name
                $vendor->setName($request->get('name'), $user);

                // Updating a Vendor will automatically enable it
                $vendor->enabled(TRUE);

                // Get entity manager
                $em = $this->get('doctrine.orm.entity_manager');

                // Push the updated Vendor to database
                $em->persist($vendor);
                $em->flush();

                // Set up the response
                $results = array(
                    'result' => 'success',
                    'message' => 'Successfully updated ' . $vendorOldName . ' to ' . $vendor->getName(),
                    'object' => $vendor
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));

            } else {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => 'Another Vendor already has update name: ' . $newVendorName,
                    'object' => NULL
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
            }
        }
    }

    /**
     * @Route("/vendor/{id}/enable", name="enableVendor")
     * @Method({"PUT"})
     */
    public function enableVendorAction(Request $request, $id) {
        // Get the Vendor repository
        $vendorRepository = $this->getDoctrine()->getRepository("AppBundle:Vendor");

        // Get the vendor by id
        $vendor = $vendorRepository->find($id);

        if (empty($vendor)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find vendor given id: ' . $id,
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Updating a Vendor will automatically enable it
            $vendor->setEnabled(TRUE, $user);

            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the updated Vendor to database
            $em->persist($vendor);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully enabled ' . $vendor->getName(),
                'object' => $vendor
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }

    }

    /**
     * @Route("/vendor/{id}/disable", name="disableVendor")
     * @Method({"PUT"})
     */
    public function disableVendorAction(Request $request, $id) {
        // Get the Vendor repository
        $vendorRepository = $this->getDoctrine()->getRepository("AppBundle:Vendor");

        // Get the vendor by id
        $vendor = $vendorRepository->find($id);

        if (empty($vendor)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find vendor given id: ' . $id,
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Updating a Vendor will automatically enable it
            $vendor->setEnabled(FALSE, $user);

            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the updated Vendor to database
            $em->persist($vendor);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully disabled ' . $vendor->getName(),
                'object' => $vendor
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }
    }

    /**
     * @Route("/vendor/{id}/delete", name="deleteVendor")
     * @Method({"DELETE"})
     *
     * TODO: Can not delete Vendor: will not cascade into Package table
     */
    public function deleteVendorAction(Request $request, $id) {
        // Get the Vendor repository
        $vendorRepository = $this->getDoctrine()->getRepository("AppBundle:Vendor");

        // Get the vendor by id
        $vendor = $vendorRepository->find($id);

        if (empty($vendor)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find vendor given id: ' . $id,
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the updated Vendor to database
            $em->remove($vendor);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully deleted Vendor: ' . $vendor->getName(),
                'object' => $vendor
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }
    }

    /**
     * @Route("/vendor/search", name="searchVendor")
     * @Method({"GET"})
     */
    public function searchVendorAction(Request $request) {
        // Get the term
        $term = $request->query->get('term');

        // Get the entity manager
        $em = $this->get('doctrine.orm.entity_manager');

        // Set up query the database for vendors that is like term
        $query = $em->createQuery(
            'SELECT v FROM AppBundle:Vendor v
            WHERE v.name LIKE :term
            AND v.enabled = :enabled'
        )->setParameters(array(
                'term' => $term.'%',
                'enabled' => 1)
        );

        // Run query and save it
        $vendor = $query->getResult();

        // If $receiver is not null, then set up $results to reflect successful query
        if (!(empty($vendor))) {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . count($vendor) . ' Vendor(s) like \'' . $term . '\'',
                'object' => $vendor
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