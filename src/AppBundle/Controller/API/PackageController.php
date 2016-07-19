<?php


namespace AppBundle\Controller\API;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Package;
use AppBundle\Entity\PackingSlip;
use Doctrine\ORM\Query\Expr;

class PackageController extends Controller
{
    /**
     * Route for creating a new Package
     *
     * @param Request $request Symfony global request variable
     *
     * @throws \Exception if Receiver/Shipper/Vendor given is not found in the database through Doctrine
     *
     * @return JsonResponse $results Results of the call
     *
     * @Route("/packages/new", name="newPackage")
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
            $results = array(
                'result' => 'error',
                'message' => '\'' . $trackingNumberOfNewPackage . '\' already exists',
                'object' => []
            );

            return new JsonResponse($results);
        } else { // Create a new Package

            // If it catches an exception while retrieving data, throw error and return
            try {
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

                $numberOfPackagesFromPOST = $request->request->get("numberOfPackages");
            } catch(\Exception $e) {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => 'Error in querying submitted data',
                    'object' => $request->request->all()
                );

                return new JsonResponse($results);

            }

            // None of the post variables can be empty
            if (empty($user) || empty($shipper) || empty($receiver) || empty($vendor) || empty($numberOfPackagesFromPOST)) {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => 'Error in processing submitted data',
                    'object' => $request->request->all()
                );

                return new JsonResponse($results);
            }

            // Create a new Package entity and set its properties
            $newPackage = new Package($trackingNumberOfNewPackage, $numberOfPackagesFromPOST, $shipper, $receiver, $vendor, $user);

            // Move and manage any uploaded files
            $uploadedFiles = [];

            // Check to see if there are any uploaded files VIA POST ($_FILES)
            if (!empty($request->files->get('attachedPackingSlips')[0])) {
                // Get the array of files uploaded through $_FILES
                $uploadedFiles = $request->files->get('attachedPackingSlips');
            }

            // If there are pictures that were uploaded, put them in an array
            if (!empty($request->request->get("packingSlipPictures"))) {
                $uploadedFiles = $this->addPicturesToUploadedFilesArray($uploadedFiles, $request->request->get("packingSlipPictures"));
            }

            // Get the current date
            $currentDate = new \DateTime("NOW");

            // Get the entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Foreach uploaded file, move it, create an entry for the packing slips table and link it to Package
            if (!(empty($uploadedFiles))) {

                foreach ($uploadedFiles as $uploadedFile) {
                    $moveUploadedFileLocation = $this->moveUploadedFile($uploadedFile, $trackingNumberOfNewPackage, $currentDate->format('Ymd'));

                    if ($moveUploadedFileLocation != NULL) {
                        $packingSlip = new PackingSlip($moveUploadedFileLocation['filename'], $moveUploadedFileLocation['extension'], $moveUploadedFileLocation['path'], $moveUploadedFileLocation['md5'], $user);

                        $em->persist($packingSlip);

                        $newPackage->addPackingSlip($packingSlip, $user);

                    } else {
                        $results = array(
                            'result' => 'error',
                            'message' => 'Error in moving uploaded file',
                            'object' => []
                        );

                        return new JsonResponse($results);
                    }
                }
            }

            // Push the new Package to database
            $em->persist($newPackage);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully created \'' . $trackingNumberOfNewPackage . '\'',
                'object' => json_decode($this->get('serializer')->serialize($newPackage, 'json'))
            );

            return new JsonResponse($results);

        }
    }

    /**
     * Route for creating editing/updating a Package
     *
     * @param Request $request Symfony global request variable
     * @param string $id Tracking number for the Package
     *
     * @throws \Exception if Receiver/Shipper/Vendor given is not found in the database through Doctrine
     *
     * @return JsonResponse $results Results of the call
     *
     * @Route("/packages/{id}/update", name="updatePackage")
     * @Method({"POST"})
     */
    public function updatePackageAction(Request $request, $id) {
        // Get the Package repository
        $packageRepository = $this->getDoctrine()->getRepository("AppBundle:Package");

        // Get the package by id
        $package = $packageRepository->find($id);

        // If package given tracking number isn't found in the repository, package doesn't exist
        if (empty($package)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find package given tracking number: ' . $id,
                'object' => []
            );

            return new JsonResponse($results);
        } else {
            // Get all that has been submitted through PUT
            $updatePackage = $request->request->all();

            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Get the entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Get the current date
            $currentDate = new \DateTime("NOW");

            // If the vendor changed, update the vendor
            if (!empty($updatePackage["vendorId"])) {
                try {
                    $vendor = $this->getDoctrine()->getRepository('AppBundle:Vendor')->find($updatePackage["vendorId"]);

                    if ($package->getVendor() != $vendor) {
                        $package->setVendor($vendor, $user);
                    }
                } catch(\Exception $e) {
                    $results = array(
                        'result' => 'error',
                        'message' => 'Error in querying Vendor',
                        'object' => []
                    );

                    return new JsonResponse($results);
                }
            }

            // If the receiver changed, update the receiver
            if (!empty($updatePackage["receiverId"])) {
                try {
                    $receiver = $this->getDoctrine()->getRepository('AppBundle:Receiver')->find($updatePackage["receiverId"]);

                    if ($package->getReceiver() != $receiver) {
                        $package->setReceiver($receiver, $user);
                    }
                } catch (\Exception $e) {
                    $results = array(
                        'result' => 'error',
                        'message' => 'Error in querying Receiver',
                        'object' => []
                    );

                    return new JsonResponse($results);
                }
            }

            // If the Shipper changed, update the Shipper
            if (!empty($updatePackage["shipperId"])) {
                try {
                    $shipper = $this->getDoctrine()->getRepository('AppBundle:Shipper')->find($updatePackage["shipperId"]);

                    if ($package->getShipper() != $shipper) {
                        $package->setShipper($shipper, $user);
                    }
                } catch (\Exception $e) {
                    $results = array(
                        'result' => 'error',
                        'message' => 'Error in querying Shipper',
                        'object' => []
                    );

                    return new JsonResponse($results);
                }
            }

            // If the number of packages changed, then update the number of packages
            if (!empty($updatePackage["numberOfPackages"])) {
                $package->setNumberOfPackages($updatePackage["numberOfPackages"], $user);
            }

            // For each updatable field
            if (!empty($updatePackage['deletePackingSlipIds'])) {
                $deletedPackingSlipIDs = $updatePackage['deletePackingSlipIds'];

                $packingSlipRepository = $this->getDoctrine()->getRepository("AppBundle:PackingSlip");

                // Remove packing slips from package
                foreach ($deletedPackingSlipIDs as $packingSlipID) {
                    $deletedPackingSlip = $packingSlipRepository->find($packingSlipID);

                    if (!empty($deletedPackingSlip)) {
                        // Get the location of where the file should be uploaded to
                        $originalPathToPackingSlip = $this->get('kernel')->getRootDir() . '/../' . $deletedPackingSlip->getRelativePath();

                        $generatedDeletedPackingSlipName = ($this->generateDeletedFileName($deletedPackingSlip->getPath(), $deletedPackingSlip->getCompleteFileName(), 0));

                        $deletedPackingSlip->renamePackingSlipToDeleted($generatedDeletedPackingSlipName);

                        $deletedPathToPackingSlip = $this->get('kernel')->getRootDir() . '/../' . $deletedPackingSlip->getRelativePath();


                        rename($originalPathToPackingSlip, $deletedPathToPackingSlip);

                        $package->removePackingSlips($deletedPackingSlip, $user);

                        // Persist the deleted file
                        $em->persist($deletedPackingSlip);
                    }
                }

            }

            $uploadedFiles = [];

            // Check to see if there are any uploaded files VIA POST ($_FILES)
            if (!empty($request->files->get('attachedPackingSlips')[0])) {
                // Get the array of files uploaded through $_FILES
                $uploadedFiles = $request->files->get('attachedPackingSlips');
            }

            // If there are pictures that were uploaded, put them in an array
            if (!empty($updatePackage["packingSlipPictures"])) {
                $uploadedFiles = $this->addPicturesToUploadedFilesArray($uploadedFiles, $updatePackage["packingSlipPictures"]);
            }

            // Foreach uploaded file, move it, create an entry for the packing slips table and link it to Package
            if (!(empty($uploadedFiles))) {
                foreach ($uploadedFiles as $uploadedFile) {
                    $moveUploadedFileLocation = $this->moveUploadedFile($uploadedFile, $id, $currentDate->format('Ymd'));

                    if ($moveUploadedFileLocation != NULL) {
                        $packingSlip = new PackingSlip($moveUploadedFileLocation['filename'], $moveUploadedFileLocation['extension'], $moveUploadedFileLocation['path'], $moveUploadedFileLocation['md5'], $user);

                        $em->persist($packingSlip);

                        $package->addPackingSlip($packingSlip, $user);

                    } else {
                        $results = array(
                            'result' => 'error',
                            'message' => 'Error in moving uploaded file',
                            'object' => []
                        );

                        return new JsonResponse($results);
                    }
                }
            }

            // Make sure the entity manager sees the entity as a new entity
            $em->persist($package);

            // Commit changes to the server
            $em->flush();

            $results = array(
                'result' => 'success',
                'message' => 'Successfully updated ' . $id . '!',
                'object' => json_decode($this->get('serializer')->serialize($package, 'json'))
            );

            // Return the results
            return new JsonResponse($results);
        }
    }

    /**
     * Route for marking a Package as delivered
     *
     * @param string $id Tracking number for Package
     *
     * @return JsonResponse Results of the call
     *
     * @Route("/packages/{id}/deliver", name="deliverPackage")
     * @Method({"PUT"})
     */
    public function deliverPackageAction($id) {
        // Get the Package repository
        $packageRepository = $this->getDoctrine()->getRepository("AppBundle:Package");

        // Get the package by id
        $package = $packageRepository->find($id);

        // If package doesn't exist, return
        if (empty($package)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find package given id: ' . $id,
                'object' => []
            );

            return new JsonResponse($results);
        } else if ($package->getDelivered()) { // If package is already delivered
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Package has already been delivered',
                'object' => []
            );

            return new JsonResponse($results);
        } if ($package->getPickedUp()) { // If package is already picked up
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Package has already been picked up' ,
                'object' => []
            );

            return new JsonResponse($results);
        } else {
            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Get the entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            $package->setDelivered(1, $user);

            // Make sure the entity manager sees the entity as a new entity
            $em->persist($package);

            // Commit changes to the server
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Package marked as delivered successfully!',
                'object' => json_decode($this->get('serializer')->serialize($package, 'json'))
            );

            return new JsonResponse($results);
        }
    }

    /**
     * Route for marking a Package as picked up
     *
     * @param Request $request Symfony global request variable
     * @param string $id Tracking number for Package
     *
     * @return JsonResponse Results of the call
     *
     * @Route("/packages/{id}/pickup", name="pickupPackage")
     * @Method({"PUT"})
     */
    public function pickupPackageAction(Request $request, $id) {

        // Get the Package repository
        $packageRepository = $this->getDoctrine()->getRepository("AppBundle:Package");

        // Get the package by id
        $package = $packageRepository->find($id);

        // If package doesn't exist, return
        if (empty($package)) {
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Can not find package given id: ' . $id,
                'object' => []
            );

            return new JsonResponse($results);
        } else if ($package->getDelivered()) { // If package is already delivered
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Package has already been delivered',
                'object' => []
            );

            return new JsonResponse($results);
        } if ($package->getPickedUp()) { // If package is already picked up
            // Set up the response
            $results = array(
                'result' => 'error',
                'message' => 'Package has already been picked up' ,
                'object' => []
            );

            return new JsonResponse($results);
        } else {
            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Get the entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            $package->setPickedUp(1, $user);
            $package->setUserWhoPickedUp($request->request->get('userWhoPickedUp'), $user);

            // Make sure the entity manager sees the entity as a new entity
            $em->persist($package);

            // Commit changes to the server
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Package marked as picked up successfully!',
                'object' => json_decode($this->get('serializer')->serialize($package, 'json'))
            );

            return new JsonResponse($results);
        }

    }

    /**
     * Route for searching up a Package base of tracking number
     *
     * @param Request $request Symfony global request variable
     *
     * @return JsonResponse Results of the call
     *
     * @Route("/packages/search", name="searchPackage")
     * @Method({"GET"})
     */
    public function searchPackageAction(Request $request) {
        // Get the term
        $term = $request->query->get('term');

        // Get the entity manager
        $em = $this->get('doctrine.orm.entity_manager');

        // Set up query the database for packages that is like term
        $query = $em->createQuery(
            'SELECT p FROM AppBundle:Package p
            WHERE p.trackingNumber = :term'
        )->setParameter('term', $term);

        // Run query and save it
        $packages = $query->getResult();

        if (empty($packages)) {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'No Package with tracking number: \'' . $term . '\'',
                'object' => []
            );
        } else {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . $term,
                'object' => json_decode($this->get('serializer')->serialize($packages, 'json'))
            );
        }

        // Return response as JSON
        return new JsonResponse($results);
    }

    /**
     * Route to search for Packages like term submitted
     *
     * @param Request $request Symfony global request variable
     *
     * @return JsonResponse Results of the call
     *
     * @Route("/packages/like", name="likePackage")
     * @Method({"Get"})
     */
    public function likePackageAction(Request $request) {
        // Get the term
        $term = $request->query->get('term');

        // Get the entity manager
        $em = $this->get('doctrine.orm.entity_manager');

        // Set up query the database for packages that is like term
        $query = $em->createQuery(
            'SELECT p FROM AppBundle:Package p
            WHERE p.trackingNumber LIKE :term'
        )->setParameter('term', $term.'%');

        // Run query and save it
        $packages = $query->getResult();

        // If $package is not null, then set up $results to reflect successful query
        if (empty($packages)) {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'No Package(s) like \'' . $term . '\'',
                'object' => []
            );

        } else {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . count($packages) . ' Package(s) like \'' . $term . '\'',
                'object' => json_decode($this->get('serializer')->serialize($packages, 'json'))
            );
        }


        // Return response as JSON
        return new JsonResponse($results);
    }

    /**
     * Route for deleting a Package
     *
     * @param string $id Tracking number for Package
     *
     * @return JsonResponse Results of the call
     *
     * @Route("/packages/{id}/delete", name="deletePackage")
     * @Method({"DELETE"})
     *
     */
//    public function deletePackageAction($id) {
//        // Get the Package repository
//        $packageRepository = $this->getDoctrine()->getRepository("AppBundle:Package");
//
//        // Get the package by id
//        $package = $packageRepository->find($id);
//
//        if (empty($package)) {
//            // Set up the response
//            $results = array(
//                'result' => 'error',
//                'message' => 'Can not find package given id: ' . $id,
//                'object' => []
//            );
//
//            return new JsonResponse($results);
//        } else {
//            // Get entity manager
//            $em = $this->get('doctrine.orm.entity_manager');
//
//            // Push the updated Package to database
//            $em->remove($package);
//            $em->flush();
//
//            // Set up the response
//            $results = array(
//                'result' => 'success',
//                'message' => 'Successfully deleted Package: ' . $package->getTrackingNumber(),
//                'object' => json_decode($this->get('serializer')->serialize($package, 'json'))
//            );
//
//            return new JsonResponse($results);
//        }
//    }

    /**
     * Route to display Package information
     *
     * @param string $id Tracking number for Package
     *
     * @return Response Render twig template with Package information
     *
     * @todo Need to finish implementing template
     *
     * @Route("/packages/{id}", name="package")
     * @Method({"GET"})
     */
    public function packageAction($id) {
        // Get the Package repository
        $packageRepository = $this->getDoctrine()->getRepository("AppBundle:Package");

        // Get the package by id
        $package = $packageRepository->find($id);

        return $this->render('entity.html.twig', [
            "type" => "package",
            "entity" => $package
        ]);
    }

    /**
     * Route to get a list of Packages based off dates
     *
     * @param Request $request Symfony global request variable
     *
     * @return JsonResponse Results of the call
     *
     * @Route("/packages", name="packages")
     * @Method({"GET"})
     */
    public function getPackagesAction(Request $request) {
        // A new time variable that points to the beginning of the day
        $dateTimeBegin = new \DateTime($request->query->get('dateBegin'));
        $dateTimeBegin->setTime(00, 00, 00);
        $dateTimeBeginString = $dateTimeBegin->format("Y-m-d H:i:s");

        // A new time variable that points to the end of the day
        $dateTimeEnd = new \DateTime($request->query->get('dateEnd'));
        $dateTimeEnd->setTime(23, 59, 59);
        $dateTimeEndString = $dateTimeEnd->format("Y-m-d H:i:s");

        // Get the repository
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->add('select', new Expr\Select(array('p')))
            ->add('from', new Expr\From('AppBundle:Package', 'p'))
            ->add('where', $qb->expr()->between(
                'p.dateReceived',
                ':dateTimeBegin',
                ":dateTimeEnd"
            )
            )->setParameters(array(
                "dateTimeBegin" => $dateTimeBeginString,
                "dateTimeEnd" => $dateTimeEndString
            ));

        $query = $qb->getQuery();

        if ($request->query->get('dateBegin') === $request->query->get('dateEnd')) {
            $results = array(
                'result' => 'error',
                'message' => 'No Packages for ' . $dateTimeBegin->format("Y-m-d"),
                'object' => []
            );
        } else {
            $results = array(
                'result' => 'error',
                'message' => 'No Packages between ' . $dateTimeBegin->format("Y-m-d") . ' and ' . $dateTimeEnd->format("Y-m-d"),
                'object' => []
            );
        }


        $queryResults = $query->getResult();

        if (!(empty($queryResults))) {
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . count($queryResults) . ' Packages',
                'object' => json_decode($this->get('serializer')->serialize($queryResults, 'json'))
            );
        }

        return new JsonResponse($results);
    }

    /**
     * Add pictures into the uploadedFiles array
     *
     * @param array $uploadedFilesArray An array with information about files uploaded
     * @param array $uploadedPicturesArray An array with information about pictures uploaded
     *
     * @throws \Exception $e when it either can't compress with gd or imagick when image compression is true
     *
     * @return array An array with both files and pictures uploaded
     */
    private function addPicturesToUploadedFilesArray($uploadedFilesArray, $uploadedPicturesArray) {
        /*
         * For each picture, do the following
         *
         * 1) Create a temp file in the tmp folder on the server
         * 2) Dump base64 code into the file
         * 3) Rename the file to include .png
         * 4) Add to the uploaded file array with information
         *
         * 5) If image_compression is on, compress/convert it to a JPEG
         */

        for ($i = 0; $i < count($uploadedPicturesArray); $i++) {
            $tmpFile = tempnam(sys_get_temp_dir(), "img");
            $image = explode(",", $uploadedPicturesArray[$i]);
            file_put_contents($tmpFile, base64_decode($image[1]));

            if (!file_exists($tmpFile)) {
                break;
            } else {
                // Image compression with gd
                if (extension_loaded('gd') && $this->getParameter('image_compression')) {
                    $image = imagecreatefrompng($tmpFile);

                    if (imagejpeg($image, $tmpFile . "_compressed", 100)) {
                        $uploadedImage = new File($tmpFile . "_compressed");
                    } else {
                        throw new \Exception("Unable to compress image with gd");
                    }

                } else if (extension_loaded('imagick') && $this->getParameter('image_compression')) { // Image compression with imagick
                    try {
                        $image = new \Imagick();

                        $image->readImage($tmpFile);
                        $image->setImageFormat("jpeg");
                        $image->setImageCompressionQuality(100);
                        $image->setImageDepth(8);
                        $image->stripImage();
                        $image->writeImage($tmpFile . "_compressed");

                        $uploadedImage = new File($tmpFile . "_compressed");
                    } catch (\Exception $e) {
                        throw new \Exception("Unable to compress image with imagick");
                    }

                } else { // No image compression (saved as a .png)
                    $uploadedImage = new File($tmpFile);
                }

                array_push($uploadedFilesArray, $uploadedImage);
            }
        }

        return $uploadedFilesArray;
    }

    /**
     * Check uploaded file for errors and file type validation then move it to the upload folder
     *
     * @param UploadedFile|File $uploadedFile The file that was uploaded
     * @param string $trackingNumber Tracking number of Package
     * @param string $date Today's date as a string
     *
     * @return null|array Null if there is an error or an array with the File's info after it is successfully moved
     */
    private function moveUploadedFile($uploadedFile, $trackingNumber, $date) {
        // Get the location of where the file should be uploaded to
        $dirRoot = $this->get('kernel')->getRootDir() . '/../';

        $path = "upload/" . $date . "/" . $trackingNumber . "/";

        $uploadedFileResults = array(
            "filename" => "",
            "extension" => "",
            "deleted" => FALSE,
            "md5" => "",
            "path" => ""
        );

        if (!(empty($uploadedFile) && empty($trackingNumber) && empty($dirRoot)) && (($uploadedFile instanceof UploadedFile) || $uploadedFile instanceof File)) {

            if (in_array($uploadedFile->guessExtension(), ['png', 'pdf', 'jpeg', 'jpg'])) {
                // If the number of uploaded files are higher than one, then edit the destination directory
                $moveDir = $dirRoot . $path;
                // If that folder doesn't exist, create it
                if (!file_exists($moveDir)) {
                    if (!(mkdir($moveDir, 0755, true))) {
                        return null;
                    }
                }

                // Add path to results
                $uploadedFileResults["path"] = $path;

                // File name is the tracking number uploaded
                $filename = $this->generateFileName($moveDir, $trackingNumber, $uploadedFile, 0);

                // If generating the filename results in false, return false
                if ($filename === FALSE) {
                    return null;
                }

                // Add filename to results
                $uploadedFileResults["filename"] = $filename;
                // Add extension to results
                if ($uploadedFile->guessExtension() === "jpeg") {
                    $uploadedFileResults["extension"] = "jpg";
                } else {
                    $uploadedFileResults["extension"] = $uploadedFile->guessExtension();
                }

                
                // Move the uploaded file to the correct directory.
                try {
                    $uploadedFile->move($moveDir, $filename);
                } catch (\Exception $e) {
                    return null;
                }

                // Attach the folder directory to the file name
                $moveToDir = $moveDir . $filename;

                $uploadedFileResults["md5"] = md5_file($moveToDir);

                return $uploadedFileResults;
            }
        } else {
            return null;
        }

        return null;
    }

    /**
     * Recursively generate a name for packing slip after moving it
     *
     * @param string $moveDir Absolute path to folder that the packing slip will be moved to
     * @param string $trackingNumber Tracking number of Package
     * @param UploadedFile|File $uploadedFile Uploaded file
     * @param int $count The number of times this function has been called
     * 
     * @return null|string Null if error or a string of the file name to be used
     */
    private function generateFileName($moveDir, $trackingNumber, $uploadedFile, $count) {
        if ($uploadedFile instanceof UploadedFile || $uploadedFile instanceof File) {
            $filename = $trackingNumber;

            if ($count > 0) {
                $filename .= '_' . $count;
            }

            /*
             * Set up the directory to where the file is going to move to and
             * change the filename of the uploaded file to the type of file it is
             * PDF = "trackingnumber".pdf
             * PNG = "trackingnumber".png
             * JPEG = "trackingnumber".jpeg
             *
             * Will need to be edited for other files, such as .tiff or .gif
             */
            switch ($uploadedFile->getMimeType()){
                case "application/pdf":
                    $filename .= ".pdf";
                    break;
                case "image/png":
                    $filename .= ".png";
                    break;
                case "image/jpeg":
                    $filename .= ".jpg";
                    break;
                default:
                    return null;
            }

            if (file_exists($moveDir . $filename)) {
                $count++;
                return $this->generateFileName($moveDir, $trackingNumber, $uploadedFile, $count);
            }

            return $filename;
        } else {
            return null;
        }

    }

    /**
     * Recursively generate a "delete" name for packing slip
     *
     * @param string $path Absolute path to folder where the file is
     * @param string $file The filename that will be changed to be "deleted"
     * @param int $count The number of times this function has been called for
     * 
     * @return string
     */
    private function generateDeletedFileName($path, $file, $count) {
        $fileNameArray = explode('.', $file);

        $filename = $fileNameArray[0] . '_DELETED';

        if ($count != 0) {
            $filename .= '(' . $count . ')';
        }

        $dirRoot = $this->get('kernel')->getRootDir() . '/../';

        if (file_exists($dirRoot . $path . $filename . '.' . $fileNameArray[1])) {
            $count++;

            return $this->generateDeletedFileName($path, $file, $count);
        }

        return $filename;
    }
}