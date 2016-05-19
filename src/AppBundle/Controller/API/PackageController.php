<?php


namespace AppBundle\Controller\API;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Package;
use AppBundle\Entity\PackingSlip;
use Doctrine\ORM\Query\Expr;

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
                'object' => NULL
            );

            return new JsonResponse($results);
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

            // None of the post variables can be empty
            if (empty($user) || empty($shipper) || empty($receiver) || empty($vendor) || empty($numOfPackagesFromPOST)) {
                // Set up the response
                $results = array(
                    'result' => 'error',
                    'message' => 'Error in processing submitted data',
                    'object' => $request->request->all()
                );

                return new JsonResponse($results);
            }

            // Create a new Package entity and set its properties
            $newPackage = new Package($trackingNumberOfNewPackage, $numOfPackagesFromPOST, $shipper, $receiver, $vendor, $user);

            // If there are pictures that were uploaded, put them in an array
            if (!empty($request->request->get("packingSlipPictures"))) {
                $uploadedPictures = $request->request->get("packingSlipPictures");
            }

            if (!empty($_FILES["attachedPackingSlips"])) {
                // Get an array of what the uploaded file object is
                $uploadedFiles = $_FILES["attachedPackingSlips"];
            }

            if (!(empty($uploadedFiles))) {
                // Moving some values around from $_FILES() so that they are aligned with the files that got uploaded
                $uploadedFiles = $this->reorganizeUploadedFiles($uploadedFiles);

                // Remove any duplicates
                $uploadedFiles = $this->removeDuplicates($uploadedFiles);
            }

            // If there are any pictures taken, move them to the uploadedFiles array
            if (!(empty($uploadedPictures))) {
                $uploadedFiles = $this->addPicturesToUploadedFilesArray($uploadedFiles, $uploadedPictures);
            }


            // If there are any pictures taken, move them to the uploadedFiles array
            if (!(empty($uploadedPictures))) {
                $uploadedFiles = $this->addPicturesToUploadedFilesArray($uploadedFiles, $uploadedPictures);
            }

            // Get the current date
            $currentDate = new \DateTime("NOW");

            // Get the entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // If the uploadedFiles array isn't empty, then check for errors and move them to the appropriate folder
            if (!(empty($uploadedFiles))) {

                $numberOfUploadedFiles = count($uploadedFiles);

                for ($i = 0; $i < $numberOfUploadedFiles; $i++) {
                    $moveUploadedFileLocation = $this->moveUploadedFile($uploadedFiles[$i], $trackingNumberOfNewPackage, $i, $currentDate->format('Ymd'));

                    if ($moveUploadedFileLocation != NULL) {
                        $packingSlip = new PackingSlip($moveUploadedFileLocation['filename'], $moveUploadedFileLocation['extension'], $moveUploadedFileLocation['path'], $moveUploadedFileLocation['md5'], $this->getUser()->getUsername());

                        $em->persist($packingSlip);

                        $newPackage->addPackingSlip($packingSlip, $user);

                    } else {
                        $results = array(
                            'result' => 'error',
                            'message' => 'Error in moving uploaded file',
                            'object' => NULL
                        );

                        return new JsonResponse($results);
                    }
                }

                // Flush packing slips to database
                $em->flush();
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
     * @Route("/package/{id}/update", name="updatePackage")
     * @Method({"PUT"})
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
                'message' => 'Can not find package given id: ' . $id,
                'object' => NULL
            );

            return new JsonResponse($results);
        } else {
            // Cannot update tracking number --> ID, userWhoReceived and dateReceived

            // Get all that has been submitted through PUT
            $updatePackage = $request->request->all();

            // Get user | anon. is temp for testing
            $user = $this->get('security.token_storage')->getToken()->getUser();

            // Get the entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Get the current date
            $currentDate = new \DateTime("NOW");

            // For each updatable field
            if (!empty($updatePackage['removedPackingSlipIds'])) {
                $deletedPackingSlipIDs = $updatePackage['removedPackingSlipIds'];

                $packingSlipRepository = $this->getDoctrine()->getRepository("AppBundle:PackingSlip");

                // Remove packing slips from package
                foreach ($deletedPackingSlipIDs as $id) {
                    $deletedPackingSlip = $packingSlipRepository->find($id);

                    if (!empty($deletedPackingSlip)) {
                        // Get the location of where the file should be uploaded to
                        $originalPathToPackingSlip = $this->get('kernel')->getRootDir() . '/../' . $deletedPackingSlip->getRelativePath();

                        $generatedDeletedPackingSlipName = ($this->generateDeletedFileName($deletedPackingSlip->getPath(), $deletedPackingSlip->getCompleteFileName(), 0));

                        $deletedPackingSlip->renamePackingSlipToDeleted($generatedDeletedPackingSlipName);

                        $deletedPathToPackingSlip = $this->get('kernel')->getRootDir() . '/../' . $deletedPackingSlip->getRelativePath();

                        // Make sure the entity manager sees the entity as a new entity
                        $em->persist($deletedPackingSlip);

                        // Commit deleted packing slips to the server
                        $em->flush();

                        rename($originalPathToPackingSlip, $deletedPathToPackingSlip);

                        $package->removePackingSlips($deletedPackingSlip);
                    }
                }

            }

            // If there are pictures that were uploaded, put them in an array
            if (!empty($updatePackage["packingSlipPictures"])) {
                $uploadedPictures = $updatePackage["packingSlipPictures"];
            }

            if (!empty($_FILES["attachedPackingSlips"])) {
                // Get an array of what the uploaded file object is
                $uploadedFiles = $_FILES["attachedPackingSlips"];

                // Moving some values around from $_FILES() so that they are aligned with the files that got uploaded
                $uploadedFiles = $this->reorganizeUploadedFiles($uploadedFiles);

                // Remove any duplicates
                $uploadedFiles = $this->removeDuplicates($uploadedFiles);
            } else {
                $uploadedFiles = [];
            }

            // If there are any pictures taken, move them to the uploadedFiles array
            if (!(empty($uploadedPictures))) {
                $uploadedFiles = $this->addPicturesToUploadedFilesArray($uploadedFiles, $uploadedPictures);
            }

            // If there are any pictures taken, move them to the uploadedFiles array
            if (!(empty($uploadedPictures))) {
                $uploadedFiles = $this->addPicturesToUploadedFilesArray($uploadedFiles, $uploadedPictures);
            }

            // If the uploadedFiles array isn't empty, then check for errors and move them to the appropriate folder
            if (!(empty($uploadedFiles))) {

                $numberOfUploadedFiles = count($uploadedFiles);

                for ($i = 0; $i < $numberOfUploadedFiles; $i++) {
                    $moveUploadedFileLocation = $this->moveUploadedFile($uploadedFiles[$i], $id, $i, $currentDate->format('Ymd'));

                    if ($moveUploadedFileLocation != NULL) {
                        $packingSlip = new PackingSlip($moveUploadedFileLocation['filename'], $moveUploadedFileLocation['extension'], $moveUploadedFileLocation['deleted'], $moveUploadedFileLocation['path'], $moveUploadedFileLocation['md5'], $user);

                        $em->persist($packingSlip);

                        $package->addPackingSlip($packingSlip, $user);

                    } else {
                        $results = array(
                            'result' => 'error',
                            'message' => 'Error in moving uploaded file',
                            'object' => NULL
                        );

                        return new JsonResponse($results);
                    }
                }

                // Flush packing slips to database
                $em->flush();
            }

            // If the vendor changed, update the vendor
            if (!empty($updatePackage["vendorId"])) {
                $vendor = $this->getDoctrine()->getRepository('AppBundle:Vendor')->find($updatePackage["vendorId"]);

                if ($package->getVendor() != $vendor) {
                    $package->setVendor($vendor, $user);
                }
            }

            // If the receiver changed, update the receiver
            if (!empty($updatePackage["receiverId"])) {
                $receiver = $this->getDoctrine()->getRepository('AppBundle:Receiver')->find($updatePackage["receiverId"]);

                if ($package->getReceiver() != $receiver) {
                    $package->setReceiver($receiver, $user);
                }
            }

            // If the Shipper changed, update the Shipper
            if (!empty($updatePackage["shipperId"])) {
                $shipper = $this->getDoctrine()->getRepository('AppBundle:Shipper')->find($updatePackage["shipperId"]);

                if ($package->getShipper() != $shipper) {
                    $package->setShipper($shipper, $user);
                }
            }

            // If the number of packages changed, then update the number of packages
            if (!empty($updatePackage["numOfPackages"])) {
                $package->setNumberOfPackages($updatePackage["numOfPackages"], $user);
            }

            // If the package has been delivered or picked up, update it [can't be both]
            if (!empty($updatePackage['delivered'])) {
                $package->setDelivered($updatePackage['delivered'], $user);
            } else if (!empty($updatePackage['pickedUp'])) {
                $package->setPickedUp($updatePackage['pickedUp'], $user);
                $package->setUserWhoPickedUp($updatePackage['userWhoPickedUp'], $user);
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
     * @Route("/package/search", name="searchPackage")
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

        // If $package is not null, then set up $results to reflect successful query
        if (!(empty($packages))) {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . count($packages) . ' Package',
                'object' => json_decode($this->get('serializer')->serialize($packages, 'json'))
            );

            // Return response as JSON
            return new JsonResponse($results);
        } else {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'Did not find packages with tracking number: ' . $term,
                'object' => NULL
            );

            // Return response as JSON
            return new JsonResponse($results);
        }
    }

    /**
     * @Route("/package/like", name="likePackage")
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
        if (!(empty($packages))) {
            // Set up response
            $results = array(
                'result' => 'success',
                'message' => 'Retrieved ' . count($packages) . ' Package(s) like \'' . $term . '\'',
                'object' => json_decode($this->get('serializer')->serialize($packages, 'json'))
            );

            // Return response as JSON
            return new JsonResponse($results);
        } else {
            // Set up response
            $results = array(
                'result' => 'error',
                'message' => 'Did not find packages with tracking number like: ' . $term,
                'object' => NULL
            );

            // Return response as JSON
            return new JsonResponse($results);
        }
    }

    /**
     * @Route("/package/{id}/delete", name="deletePackage")
     * @Method({"DELETE"})
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

            return new JsonResponse($results);
        } else {
            // Get entity manager
            $em = $this->get('doctrine.orm.entity_manager');

            // Push the updated Package to database
            $em->remove($package);
            $em->flush();

            // Set up the response
            $results = array(
                'result' => 'success',
                'message' => 'Successfully deleted Package: ' . $package->getTrackingNumber(),
                'object' => json_decode($this->get('serializer')->serialize($package, 'json'))
            );

            return new JsonResponse($results);
        }
    }

    /**
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
                'object' => NULL
            );
        } else {
            $results = array(
                'result' => 'error',
                'message' => 'No Packages between ' . $dateTimeBegin->format("Y-m-d") . ' and ' . $dateTimeEnd->format("Y-m-d"),
                'object' => NULL
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
     * @Route("/package/{id}", name="package")
     * @Method({"GET"})
     */
    public function packageAction(Request $request, $id) {
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
     * Resort $_FILES array so that each element has its own information
     *
     * @param $uploadedFilesArray - An array with information about files uploaded
     * @return array - A reorganized array
     */
    private function reorganizeUploadedFiles($uploadedFilesArray) {
        $organizedUploadedFilesArray[] = array();

        for ($i = 0; $i < count($uploadedFilesArray["name"]); $i++) {
            if ($uploadedFilesArray["name"][$i] != "") {
                $organizedUploadedFilesArray[$i]["name"] = $uploadedFilesArray["name"][$i];
                $organizedUploadedFilesArray[$i]["type"] = $uploadedFilesArray["type"][$i];
                $organizedUploadedFilesArray[$i]["tmp_name"] = $uploadedFilesArray["tmp_name"][$i];
                $organizedUploadedFilesArray[$i]["error"] = $uploadedFilesArray["error"][$i];
                $organizedUploadedFilesArray[$i]["size"] = $uploadedFilesArray["size"][$i];
            }
        }

        return array_values(array_filter($organizedUploadedFilesArray));
    }

    /**
     * Find duplicate uploaded files
     *
     * @param $uploadedFilesArray - An array with information about files uploaded
     * @return array - An array without any duplicates
     */
    private function removeDuplicates($uploadedFilesArray) {
        $uniqueArray = array();

        foreach ($uploadedFilesArray as $uploadedFile) {
            $unique = TRUE;
            $numberOfUniqueFiles = count($uniqueArray);

            for ($i = 0; $i < $numberOfUniqueFiles; $i++) {
                if (md5_file($uniqueArray[$i]["tmp_name"]) === md5_file($uploadedFile["tmp_name"])) {
                    $unique = FALSE;
                    break;
                }
            }

            if ($unique) {
                array_push($uniqueArray, $uploadedFile);
            }
        }

        return $uniqueArray;
    }

    /**
     * Add pictures into the uploadedFiles array
     *
     * @param $uploadedFilesArray - An array with information about files uploaded
     * @param $uploadedPicturesArray - An array with information about pictures uploaded
     * @return array - An array with both files and pictures uploaded
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
         */

        for ($i = 0; $i < count($uploadedPicturesArray); $i++) {
            $tmpFile = tempnam(sys_get_temp_dir(), "img");
            $image = explode(",", $uploadedPicturesArray[$i]);
            file_put_contents($tmpFile, base64_decode($image[1]));

            if (!file_exists($tmpFile)) {
                break;
            } else {
                $tmpFileName = explode("/", $tmpFile);
                $tmpFileToAddToArray = array(
                    "name" => $tmpFileName[count($tmpFileName) - 1] . ".png",
                    "type" => "image/png",
                    "tmp_name" => $tmpFile,
                    "error" => 0,
                    "size" => filesize($tmpFile)
                );

                array_push($uploadedFilesArray, $tmpFileToAddToArray);
            }
        }

        return array_values(array_filter($uploadedFilesArray));
    }

    /**
     * Check uploaded file for errors and file type validation then move it to the upload folder
     *
     * @param $uploadedFile
     * @param $trackingNumber
     * @param $count
     * @param $date
     *
     * @return Array = $results
     */
    private function moveUploadedFile($uploadedFile, $trackingNumber, $count, $date) {
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

        if (!(empty($uploadedFile) && empty($trackingNumber) && ($count < 0) && empty($dirRoot))) {
            if (!($this->checkUploadedFileForErrors($uploadedFile)) && ($this->checkUploadedFileForValidFileType($uploadedFile))) {
                // If the number of uploaded files are higher than one, then edit the destination directory
                $moveDir = $dirRoot . $path;
                // If that folder doesn't exist, create it
                if (!file_exists($moveDir)) {
                    if (!(mkdir($moveDir, 0755, true))) {
                        $folderWithoutRootDirectory = strstr($moveDir, '/upload');
                        $this->logger->error('Unable to create ...' . $folderWithoutRootDirectory . ' on the server');
                        return FALSE;
                    }
                }

                // Add path to results
                $uploadedFileResults["path"] = $path;

                // File name is the tracking number uploaded
                $filename = $this->generateFileName($moveDir, $trackingNumber, $uploadedFile, 0);

                // If generating the filename results in false, return false
                if ($filename === FALSE) {
                    return FALSE;
                }

                // Add filename to results
                $uploadedFileResults["filename"] = $filename;

                // Attach the folder directory to the file name
                $moveToDir = $moveDir . $filename;

                /*
                 * Move the uploaded file to the correct directory.
                 * If the file is an uploaded file (VIA POST), then move it.
                 * Else if it's an image, then manually move it and change permissions on the image to read-write, read, read.
                 */
                if (is_uploaded_file($uploadedFile["tmp_name"])) {
                    if (!(move_uploaded_file($uploadedFile["tmp_name"], $moveToDir))) {
                        $folderWithoutRootDirectory = strstr($moveToDir, '/upload');
                        $this->logger->error('Unable to move uploaded file(s) from temporary folder to ...' . $folderWithoutRootDirectory);
                        return FALSE;
                    }
                } else if (($uploadedFile["type"] == "image/png") || ($uploadedFile["type"] == "image/jpeg")) {
                    if (!(rename($uploadedFile["tmp_name"], $moveToDir))) {
                        $folderWithoutRootDirectory = strstr($moveToDir, '/upload');
                        $this->logger->error('Unable to move uploaded file(s) from temporary folder to ...' . $folderWithoutRootDirectory);
                        return FALSE;
                    }

                    if (!chmod($moveToDir, 0644)) {
                        $folderWithoutRootDirectory = strstr($moveToDir, '/upload');
                        $this->logger->error('Unable to move uploaded file(s) from temporary folder to ...' . $folderWithoutRootDirectory);
                        return FALSE;
                    }
                }

                // Add extension to results
                if (($uploadedFile["type"] == "image/png")) {
                    $uploadedFileResults["extension"] = "png";
                } else if ($uploadedFile["type"] == "image/jpeg") {
                    $uploadedFileResults["extension"] = "jpeg";
                } else if (($uploadedFile["type"] == "application/pdf")) {
                    $uploadedFileResults["extension"] = "pdf";
                } else {
                    return FALSE;
                }


                // Existence of file
                if (!file_exists($moveToDir)) {
                    $folderWithoutRootDirectory = strstr($moveToDir, '/upload');
                    $this->logger->error('The file that got uploaded does not exist at location ...' . $folderWithoutRootDirectory);
                    return FALSE;
                }

                $uploadedFileResults["md5"] = md5_file($moveToDir);

                return $uploadedFileResults;
            }
        } else {
            return FALSE;
        }

        return FALSE;
    }

    /**
     * Check uploaded file from form for errors
     *
     * @param $uploadedFile
     * @return Boolean
     */
    private function checkUploadedFileForErrors($uploadedFile) {
        // Check to see if there are any errors with the uploaded file
        if ($uploadedFile["error"] > 0 ) {
            // If there's an error, return the error to the page and log the error
            switch ($uploadedFile["error"]) {
                case UPLOAD_ERR_INI_SIZE:
                    $this->logger->error('Error: The file is too big: (ERR_CODE: ' . $uploadedFile["error"] . ')');

                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $this->logger->error('Error: The form submitted is too big: (ERR_CODE: ' . $uploadedFile["error"] . ')');

                    break;
                case UPLOAD_ERR_PARTIAL:
                    $this->logger->error('Error: The file was partially uploaded. (ERR_CODE: ' . $uploadedFile["error"] . ')');

                    break;
                case UPLOAD_ERR_NO_FILE:
                    $this->logger->error('Error:  No file was uploaded. (ERR_CODE: ' . $uploadedFile["error"] . ')');

                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $this->logger->error('Error: Missing temporary folder for uploads. (ERR_CODE: ' . $uploadedFile["error"] . ')');

                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $this->logger->error('Error: Can not save file onto server. (ERR_CODE: ' . $uploadedFile["error"] . ')');

                    break;
                case UPLOAD_ERR_EXTENSION:
                    $this->logger->error('Error: Invalid extension. (ERR_CODE: ' . $uploadedFile["error"] . ')');

                    break;
                default:
                    $this->logger->error('Error: Invalid document. (ERR_CODE: ' . $uploadedFile["error"] . ')');

                    break;
            }

            return TRUE;
        }

        return FALSE;
    }

    /**
     * Check the uploaded file to see if its a valid file type
     *
     * @param $uploadedFile
     * @return Boolean
     */
    private function checkUploadedFileForValidFileType($uploadedFile) {
        // Files MIME types that are allowed
        $allowedFiles = array(
            "application/pdf",
            "image/png",
            "image/jpeg"
        );

        // Validate files base on MIME types on server side
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        // Check to see if the file type is allowed to be uploaded
        if (!(in_array(finfo_file($finfo, $uploadedFile["tmp_name"]), $allowedFiles))) {
            return FALSE;
        }

        return TRUE;
    }

    public function removeDuplicatesFromServer($packingSlipString) {
        // Get the location of where the files are
        $dirRoot = $this->get('kernel')->getRootDir() . '/../';

        $packingSlipsArray = explode(',', $packingSlipString);

        $numberOfPackingSlips = count($packingSlipsArray);

        for ($i = 0; $i < $numberOfPackingSlips; $i++) {
            $packingSlip1 = $dirRoot . $packingSlipsArray[$i];

            for ($j = $i + 1; $j < $numberOfPackingSlips; $j++) {
                $packingSlip2 = $dirRoot . $packingSlipsArray[$j];

                if (file_exists($packingSlip1) && file_exists($packingSlip2)) {
                    if (md5_file($packingSlip1) === md5_file($packingSlip2)) {
                        unlink($packingSlip2);
                    }
                }
            }
        }

        for ($i = 0; $i < $numberOfPackingSlips; $i++) {
            $packingSlip1 = $dirRoot . $packingSlipsArray[$i];

            if (!file_exists($packingSlip1)) {
                unset($packingSlipsArray[$i]);
            }
        }

        return array_values($packingSlipsArray);
    }

    private function generateFileName($moveDir, $trackingNumber, $uploadedFile, $count) {
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
        switch ($uploadedFile["type"]){
            case "application/pdf":
                $filename .= ".pdf";
                break;
            case "image/png":
                $filename .= ".png";
                break;
            case "image/jpeg":
                $filename .= ".jpeg";
                break;
            default:
                return FALSE;
        }

        if (file_exists($moveDir . $filename)) {
            $count++;
            return $this->generateFileName($moveDir, $trackingNumber, $uploadedFile, $count);
        }

        return $filename;
    }

    public function generateDeletedFileName($path, $file, $count) {
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