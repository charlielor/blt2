<?php


namespace AppBundle\Controller\API;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Package;

class PackageController extends Controller
{
    /**
     * @Route("/package/new", name="newPackage")
     * @Method({"POST"})
     */
    public function newPackageAction(Request $request) {
        // Tracking number for new Package
        $trackingNumberOfNewPackage = $request->request->get("trackingNumber");

        // Get the Package repository
        $packageRepository = $this->getDoctrine()->getRepository("AppBundle:Package");

        // Does the new Package's tracking number already exists in database
        $existingPackageGivenTrackingNumber = $packageRepository->findBy([
            "trackingNumber" => $trackingNumberOfNewPackage
        ]);

        // If the query is not empty, a Package with the given name already exists
        if (!empty($existingPackageGivenTrackingNumber)) {
            // Package already exists
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => '\'' . $trackingNumberOfNewPackage . '\' already exists',
                'object' => $existingPackageGivenTrackingNumber
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else { // Create a new Package

            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Get the shipper
            $shipper = $this->getDoctrine()->getRepository("AppBundle:Shipper")
                ->find($request->request->get("shipperId"));

            // Get the receiver
            $receiver = $this->getDoctrine()->getRepository("AppBundle:Receiver")
                ->find($request->request->get("receiverId"));

            // Get the shipper
            $vendor = $this->getDoctrine()->getRepository("AppBundle:Vendor")
                ->find($request->request->get("vendorId"));

            $numOfPackagesFromPOST = $request->request->get("numOfPackages");

            // Create a new Package entity and set its properties
            $newPackage = new Package($trackingNumberOfNewPackage, $numOfPackagesFromPOST, $shipper, $receiver, $vendor, $user);

            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the new Package to database
            $em->persist($newPackage);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully created \'' . $trackingNumberOfNewPackage . '\'',
                'object' => $newPackage
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }


    }

    /**
     * @Route("/package/{id}/update", name="updatePackage")
     * @Method({"PUT"})
     */
    public function updatePackageAction(Request $request, $id) {
        // Get the Package repository
        $packageRepository = $this->getDoctrine()->getRepository("AppBundle:Package");

        // Get the package by id
        $package = $packageRepository->find($id);

        if (empty($package)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find package given id: ' . $id,
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            $newPackageName = $request->request->get("trackingNumber");

            // Existing Package with the same name
            $existingPackageGivenName = $packageRepository->findBy(array('name' => $newPackageName));

            // If no other Package has the same name, then update it
            if (empty($existingPackageGivenName)) {
                $packageOldName = $package->getName();

                // Get user | anon. is temp for testing
                $user = $this->get('security.token_storage')->getToken()->getUser();

                // Set the current Package to its new name
                $package->setName($request->get('name'), $user);

                // Updating a Package will automatically enable it
                $package->enabled(TRUE);

                // Get entity manager
                $em = $this->get('doctrine.orm.entity_manager');

                // Push the updated Package to database
                $em->persist($package);
                $em->flush();

                // Set up the response
                $results = array(
                    'result' => 'success',
                    'message' => 'Successfully updated ' . $packageOldName . ' to ' . $package->getName(),
                    'object' => $package
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));

            } else {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => 'Another Package already has update name: ' . $newPackageName,
                    'object' => NULL
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
            }
        }
    }

    /**
     * @Route("/package/{id}/delete", name="deletePackage")
     * @Method({"PUT"})
     *
     */
    public function deletePackageAction(Request $request, $id) {
        // Get the Package repository
        $packageRepository = $this->getDoctrine()->getRepository("AppBundle:Package");

        // Get the package by id
        $package = $packageRepository->find($id);

        if (empty($package)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find package given id: ' . $id,
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the updated Package to database
            $em->remove($package);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully deleted Package: ' . $package->getName(),
                'object' => $package
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }
    }

    /**
     * @Route("/package/search", name="searchPackage")
     * @Method({"GET"})
     */
    public function searchPackageAction(Request $request) {
        // Get the term
        $term = $request->query->get('term');

        // Get the entity manager
        $em = $this->get('doctrine.orm.entity_manager');

        // Set up query the database for packages that is like terms
        $query = $em->createQuery(
            'SELECT v FROM AppBundle:Package v
            WHERE v.name LIKE :term
            AND v.enabled = :enabled'
        )->setParameters(array(
                'term' => $term.'%',
                'enabled' => 1)
        );

        // Run query and save it
        $package = $query->getResult();

        // If $package is not null, then set up $results to reflect successful query
        if (!(empty($package))) {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . count($package) . ' Package(s) like \'' . $term . '\'',
                'object' => $package
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