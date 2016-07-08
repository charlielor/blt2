<?php


namespace AppBundle\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Vendor;

class VendorController extends Controller
{
    /**
     * @Route("/vendors/new", name="newVendor")
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
                    'object' => []
                );

                return new JsonResponse($results);
            } else {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => '\'' . $nameOfNewVendor . '\' already exists',
                    'object' => []
                );

                return new JsonResponse($results);
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

            // Get the new vendor and return it
            $submittedVendor = $vendorRepository->findBy(["name" => $nameOfNewVendor]);

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully created \'' . $nameOfNewVendor . '\'',
                'object' => json_decode($this->get('serializer')->serialize($submittedVendor, 'json'))
            );

            return new JsonResponse($results);
        }


    }

    /**
     * @Route("/vendors/{id}/update", name="updateVendor")
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
                'object' => []
            );

            return new JsonResponse($results);
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
                $vendor->setEnabled(TRUE, $user);

                // Get entity manager
                $em = $this->get('doctrine.orm.entity_manager');

                // Push the updated Vendor to database
                $em->persist($vendor);
                $em->flush();

                // Get the new vendor and return it
                $submittedVendor = $vendorRepository->findBy(["name" => $newVendorName]);

                // Set up the response
                $results = array(
                    'result' => 'success',
                    'message' => 'Successfully updated ' . $vendorOldName . ' to ' . $vendor->getName(),
                    'object' => json_decode($this->get('serializer')->serialize($submittedVendor, 'json'))
                );

                return new JsonResponse($results);

            } else {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => 'Another Vendor already has update name: ' . $newVendorName,
                    'object' => []
                );

                return new JsonResponse($results);
            }
        }
    }

    /**
     * @Route("/vendors/{id}/enable", name="enableVendor")
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
                'object' => []
            );

            return new JsonResponse($results);
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
                'object' => json_decode($this->get('serializer')->serialize($vendor, 'json'))
            );

            return new JsonResponse($results);
        }

    }

    /**
     * @Route("/vendors/{id}/disable", name="disableVendor")
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
                'object' => []
            );

            return new JsonResponse($results);
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
                'object' => json_decode($this->get('serializer')->serialize($vendor, 'json'))
            );

            return new JsonResponse($results);
        }
    }

    /**
     * @Route("/vendors/{id}/delete", name="deleteVendor")
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
                'object' => []
            );

            return new JsonResponse($results);
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
                'object' => json_decode($this->get('serializer')->serialize($vendor, 'json'))
            );

            return new JsonResponse($results);
        }
    }

    /**
     * @Route("/vendors/search", name="searchVendor")
     * @Method({"GET"})
     */
    public function searchVendorAction(Request $request) {
        // Get the term
        $term = $request->query->get('term');

        // Get the entity manager
        $em = $this->get('doctrine.orm.entity_manager');

        // Set up query the database for vendors that is the term
        $query = $em->createQuery(
            'SELECT v FROM AppBundle:Vendor v
            WHERE v.name = :term
            AND v.enabled = :enabled'
        )->setParameters(array(
                'term' => $term,
                'enabled' => 1)
        );

        // Run query and save it
        $vendor = $query->getResult();

        if (empty($vendor)) {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'No Vendor with the name: ' . $term,
                'object' => json_decode($this->get('serializer')->serialize($vendor, 'json'))
            );

        } else {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . $term,
                'object' => json_decode($this->get('serializer')->serialize($vendor, 'json'))
            );

        }

        // Return response as JSON
        return new JsonResponse($results);
    }

    /**
     * @Route("/vendors/like", name="likeVendor")
     * @Method({"GET"})
     */
    public function likeVendorAction(Request $request) {
        // Get the term
        $term = $request->query->get('term');

        // Get the entity manager
        $em = $this->get('doctrine.orm.entity_manager');

        // Set up query the database for vendors that is like term
        $query = $em->createQuery(
            'SELECT v FROM AppBundle:Vendor v
            WHERE v.name LIKE :term'
        )->setParameter('term', $term.'%');

        // Run query and save it
        $vendor = $query->getResult();

        if (empty($vendor)) {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'No Vendor(s) like \'' . $term . '\'',
                'object' => json_decode($this->get('serializer')->serialize($vendor, 'json'))
            );
        } else {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . count($vendor) . ' Vendor(s) like \'' . $term . '\'',
                'object' => json_decode($this->get('serializer')->serialize($vendor, 'json'))
            );
        }

        // Return response as JSON
        return new JsonResponse($results);
    }

    /**
     * @Route("/vendors", name="vendors")
     * @Method({"GET"})
     */
    public function allVendorsAction() {
        // Get the Vendor repository
        $vendorRepository = $this->getDoctrine()->getRepository("AppBundle:Vendor");

        // Get all enabled Vendors
        $vendors = $vendorRepository->findAll();

        // Set up the response
        $results = array(
            'result' => 'success',
            'message' => 'Successfully retrieved all Vendors',
            'object' => json_decode($this->get('serializer')->serialize($vendors, 'json'))
        );

        return new JsonResponse($results);
    }

    /**
     * @Route("/vendors/{id}", name="vendor")
     * @Method({"GET"})
     */
    public function vendorAction($id) {
        // Get the Vendor repository
        $vendorRepository = $this->getDoctrine()->getRepository("AppBundle:Vendor");

        // Get the enabled Vendors
        $vendor = $vendorRepository->find($id);

        return $this->render('entity.html.twig', [
            "type" => "vendor",
            "entity" => $vendor
        ]);
    }
}