<?php


namespace AppBundle\Controller\Frontend;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ReportingController extends Controller
{
    /**
     * @Route("/reporting", name="reporting")
     */
    public function renderTemplateAction() {
        return $this->render('reporting.html.twig');
    }

    /**
     * @Route("/reporting/queryRequest", name="queryRequest")
     */
    public function queryRequestAction(Request $request) {
        $requestQuery = $request->get("request");

        $typeOfRequest = $request->get("type");

        $requestExplode = explode("-", $requestQuery);

        $typeOfRequestExplode = explode("-", $typeOfRequest);

        $tokenId = $request->get("tokenId");

        // TODO - Find a way to limit dates and/or data returned
        // A new time variable that points to the beginning of the day
        $dateTimeBegin = new \DateTime($request->get('dateBegin'));
        $dateTimeBegin->setTime(00, 00, 00);
        $dateTimeBeginString = $dateTimeBegin->format("Y-m-d H:i:s");

        // A new time variable that points to the end of the day
        $dateTimeEnd = new \DateTime($request->get('dateEnd'));
        $dateTimeEnd->setTime(23, 59, 59);
        $dateTimeEndString = $dateTimeEnd->format("Y-m-d H:i:s");


        // Calculate the number of days between two given dates and add one to include the end date
        $dateDiff = (integer)$dateTimeEnd->diff($dateTimeBegin)->format("%a") + 1;

        $firstCutOffForDateDiff = 22;
        $secondCutOffForDateDiff = 121;

        $em = $this->get('doctrine.orm.entity_manager');
        $query = null;
        $packages = null;

        /*
         * r = anything receiving
         * d = anything delivering
         * p = anything pickup
         */
        switch ($requestExplode[0]) {
            // Requests for anything relating to packages that have been received
            case 'r':
                switch ($requestExplode[1]) {
                    /*
                     * Get all received packages from Vendor group by dates
                     */
                    case 0:
                        $query = null;

                        if ($typeOfRequestExplode[0] == "g") {
//                            $query = $em->createQuery(
//                                'SELECT DATE(p.dateReceived) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
//                             WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
//                             AND p.vendor = :vendorId
//                             GROUP BY d'
//                            )->setParameters(array(
//                                "dateTimeBegin" => $dateTimeBeginString,
//                                "dateTimeEnd" => $dateTimeEndString,
//                                "vendorId" => $tokenId
//                            ));

                            // Depending on the number of dates between given dates, get the following queries: $firstCutOffForDateDiff days, $firstCutOffForDateDiff weeks or $firstCutOffForDateDiff months
                            // Date difference is less than two weeks
                            if ($dateDiff < $firstCutOffForDateDiff) {
                                $query = $em->createQuery(
                                    'SELECT DATE(p.dateReceived) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.vendor = :vendorId
                                     GROUP BY d'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                    "vendorId" => $tokenId
                                ));
                            } else if ($dateDiff < $secondCutOffForDateDiff) { // Date difference is between two weeks and three months
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateReceived) AS y, WEEK(p.dateReceived, 3) AS w, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.vendor = :vendorId
                                     GROUP BY y, w'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                    "vendorId" => $tokenId
                                ));
                            } else { // Date difference is greater than three months and one day
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateReceived) AS y, MONTH(p.dateReceived) AS m, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.vendor = :vendorId
                                     GROUP BY y, m'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                    "vendorId" => $tokenId
                                ));
                            }
                        } else if ($typeOfRequestExplode[0] == "t" || $typeOfRequestExplode[0] == "d") { // Get the packages and their details for table/download
                            $query = $em->createQuery(
                                'SELECT p.trackingNumber, p.numberOfPackages, p.delivered, p.dateDelivered, p.dateReceived, p.userWhoReceived, p.userWhoDelivered, s.name AS shipperName, r.name AS receiverName, v.name AS vendorName, p.pickedUp, p.datePickedUp, p.userWhoPickedUp, p.userWhoAuthorizedPickUp
                             FROM AppBundle:Package p, AppBundle:Shipper s, AppBundle:Receiver r, AppBundle:Vendor v
                             WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                             AND p.shipper = s.id
                             AND p.receiver = r.id
                             AND p.vendor = v.id
                             AND p.vendor = :tokenId
                             ORDER BY p.dateReceived'
                            )->setParameters(array(
                                "dateTimeBegin" => $dateTimeBeginString,
                                "dateTimeEnd" => $dateTimeEndString,
                                "tokenId" => $tokenId
                            ));

                        }

                        break;
                    /*
                     * Get all received packages received by User
                     */
                    case 1:
                        if ($typeOfRequestExplode[0] == "g") {
//                            $query = $em->createQuery(
//                                'SELECT DATE(p.dateReceived) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
//                             WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
//                             AND p.userWhoReceived = :userWhoReceived
//                             GROUP BY d'
//                            )->setParameters(array(
//                                "dateTimeBegin" => $dateTimeBeginString,
//                                "dateTimeEnd" => $dateTimeEndString,
//                                "userWhoReceived" => $tokenId
//                            ));

                            // Date difference is less than two weeks
                            if ($dateDiff < $firstCutOffForDateDiff) {
                                $query = $em->createQuery(
                                    'SELECT DATE(p.dateReceived) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.userWhoReceived = :userWhoReceived
                                     GROUP BY d'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                    "userWhoReceived" => $tokenId
                                ));
                            } else if ($dateDiff < $secondCutOffForDateDiff) { // Date difference is between two weeks and three months
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateReceived) AS y, WEEK(p.dateReceived, 3) AS w, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.userWhoReceived = :userWhoReceived
                                     GROUP BY y, w'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                    "userWhoReceived" => $tokenId
                                ));
                            } else { // Date difference is greater than three months and one day
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateReceived) AS y, MONTH(p.dateReceived) AS m, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.userWhoReceived = :userWhoReceived
                                     GROUP BY y, m'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                    "userWhoReceived" => $tokenId
                                ));
                            }
                        } else if ($typeOfRequestExplode[0] == "t" || $typeOfRequestExplode[0] == "d") { // Get the packages and their details for table/download
                            $query = $em->createQuery(
                                'SELECT p.trackingNumber, p.numberOfPackages, p.delivered, p.dateDelivered, p.dateReceived, p.userWhoReceived, p.userWhoDelivered, s.name AS shipperName, r.name AS receiverName, v.name AS vendorName, p.pickedUp, p.datePickedUp, p.userWhoPickedUp, p.userWhoAuthorizedPickUp
                             FROM AppBundle:Package p, AppBundle:Shipper s, AppBundle:Receiver r, AppBundle:Vendor v
                             WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                             AND p.shipper = s.id
                             AND p.receiver = r.id
                             AND p.vendor = v.id
                             AND p.userWhoReceived = :tokenId
                             ORDER BY p.dateReceived'
                            )->setParameters(array(
                                "dateTimeBegin" => $dateTimeBeginString,
                                "dateTimeEnd" => $dateTimeEndString,
                                "tokenId" => $tokenId
                            ));

                        }

                        break;
                    /*
                     * Get all received packages delivered by Shipper group by Date
                     */
                    case 2:
                        if ($typeOfRequestExplode[0] == "g") {
//                            $query = $em->createQuery(
//                                'SELECT DATE(p.dateReceived) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
//                             WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
//                             AND p.shipper = :shipperId
//                             GROUP BY d'
//                            )->setParameters(array(
//                                "dateTimeBegin" => $dateTimeBeginString,
//                                "dateTimeEnd" => $dateTimeEndString,
//                                "shipperId" => $tokenId
//                            ));

                            // Date difference is less than two weeks
                            if ($dateDiff < $firstCutOffForDateDiff) {
                                $query = $em->createQuery(
                                    'SELECT DATE(p.dateReceived) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.shipper = :shipperId
                                     GROUP BY d'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                    "shipperId" => $tokenId
                                ));
                            } else if ($dateDiff < $secondCutOffForDateDiff) { // Date difference is between two weeks and three months
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateReceived) AS y, WEEK(p.dateReceived, 3) AS w, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.shipper = :shipperId
                                     GROUP BY y, w'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                    "shipperId" => $tokenId
                                ));
                            } else { // Date difference is greater than three months and one day
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateReceived) AS y, MONTH(p.dateReceived) AS m, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.shipper = :shipperId
                                     GROUP BY y, m'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                    "shipperId" => $tokenId
                                ));
                            }
                        } else if ($typeOfRequestExplode[0] == "t" || $typeOfRequestExplode[0] == "d") { // Get the packages and their details for table/download
                            $query = $em->createQuery(
                                'SELECT p.trackingNumber, p.numberOfPackages, p.delivered, p.dateDelivered, p.dateReceived, p.userWhoReceived, p.userWhoDelivered, s.name AS shipperName, r.name AS receiverName, v.name AS vendorName, p.pickedUp, p.datePickedUp, p.userWhoPickedUp, p.userWhoAuthorizedPickUp
                             FROM AppBundle:Package p, AppBundle:Shipper s, AppBundle:Receiver r, AppBundle:Vendor v
                             WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                             AND p.shipper = s.id
                             AND p.receiver = r.id
                             AND p.vendor = v.id
                             AND p.shipper = :tokenId
                             ORDER BY p.dateReceived'
                            )->setParameters(array(
                                "dateTimeBegin" => $dateTimeBeginString,
                                "dateTimeEnd" => $dateTimeEndString,
                                "tokenId" => $tokenId
                            ));

                        }

                        break;
                    case 3:
                        /*
                         * Get all received packages group by dates
                         */
                        $query = null;

                        if ($typeOfRequestExplode[0] == "g") {
//                            $query = $em->createQuery(
//                                'SELECT DATE(p.dateReceived) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
//                             WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
//                             AND p.vendor = :vendorId
//                             GROUP BY d'
//                            )->setParameters(array(
//                                "dateTimeBegin" => $dateTimeBeginString,
//                                "dateTimeEnd" => $dateTimeEndString,
//                                "vendorId" => $tokenId
//                            ));

                            // Depending on the number of dates between given dates, get the following queries: $firstCutOffForDateDiff days, $firstCutOffForDateDiff weeks or $firstCutOffForDateDiff months
                            // Date difference is less than two weeks
                            if ($dateDiff < $firstCutOffForDateDiff) {
                                $query = $em->createQuery(
                                    'SELECT DATE(p.dateReceived) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     GROUP BY d'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                ));
                            } else if ($dateDiff < $secondCutOffForDateDiff) { // Date difference is between two weeks and three months
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateReceived) AS y, WEEK(p.dateReceived, 3) AS w, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     GROUP BY y, w'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                ));
                            } else { // Date difference is greater than three months and one day
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateReceived) AS y, MONTH(p.dateReceived) AS m, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     GROUP BY y, m'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                ));
                            }
                        } else if ($typeOfRequestExplode[0] == "t" || $typeOfRequestExplode[0] == "d") { // Get the packages and their details for table/download
                            $query = $em->createQuery(
                                'SELECT p.trackingNumber, p.numberOfPackages, p.delivered, p.dateDelivered, p.dateReceived, p.userWhoReceived, p.userWhoDelivered, s.name AS shipperName, r.name AS receiverName, v.name AS vendorName, p.pickedUp, p.datePickedUp, p.userWhoPickedUp, p.userWhoAuthorizedPickUp
                             FROM AppBundle:Package p, AppBundle:Shipper s, AppBundle:Receiver r, AppBundle:Vendor v
                             WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                             AND p.shipper = s.id
                             AND p.receiver = r.id
                             AND p.vendor = v.id
                             ORDER BY p.dateReceived'
                            )->setParameters(array(
                                "dateTimeBegin" => $dateTimeBeginString,
                                "dateTimeEnd" => $dateTimeEndString,
                            ))->setMaxResults(15000);

                        }

                        break;

                    default:
                        $results = array(
                            'result' => 'error',
                            'message' => 'Unable to determine request type',
                            'object' => NULL
                        );

                        return new JsonResponse($this->get('serializer')->serialize($results, 'json'));

                }

                break;
            // Requests for anything relating to packages that have been delivered
            case 'd':
                switch ($requestExplode[1]) {
                    /*
                     * Get all packages delivered to Receiver
                     */
                    case 0:
                        if ($typeOfRequestExplode[0] == "g") {
//                            $query = $em->createQuery(
//                                'SELECT DATE(p.dateDelivered) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
//                             WHERE p.dateDelivered BETWEEN :dateTimeBegin AND :dateTimeEnd
//                             AND p.receiver = :receiverId
//                             GROUP BY d'
//                            )->setParameters(array(
//                                "dateTimeBegin" => $dateTimeBeginString,
//                                "dateTimeEnd" => $dateTimeEndString,
//                                "receiverId" => $tokenId
//                            ));

                            // Date difference is less than two weeks
                            if ($dateDiff < $firstCutOffForDateDiff) {
                                $query = $em->createQuery(
                                    'SELECT DATE(p.dateDelivered) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                             WHERE p.dateDelivered BETWEEN :dateTimeBegin AND :dateTimeEnd
                             AND p.receiver = :receiverId
                             AND p.delivered = TRUE
                             GROUP BY d'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                    "receiverId" => $tokenId
                                ));
                            } else if ($dateDiff < $secondCutOffForDateDiff) { // Date difference is between two weeks and three months
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateDelivered) AS y, WEEK(p.dateDelivered, 3) AS w, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateDelivered BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.receiver = :receiverId
                                     AND p.delivered = TRUE
                                     GROUP BY y, w'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                    "receiverId" => $tokenId
                                ));
                            } else { // Date difference is greater than three months and one day
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateDelivered) AS y, MONTH(p.dateDelivered) AS m, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateDelivered BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.receiver = :receiverId
                                     AND p.delivered = TRUE
                                     GROUP BY y, m'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                    "receiverId" => $tokenId
                                ));
                            }

                        } else if ($typeOfRequestExplode[0] == "t" || $typeOfRequestExplode[0] == "d") { // Get the packages and their details for table/download
                            $query = $em->createQuery(
                                'SELECT p.trackingNumber, p.numberOfPackages, p.delivered, p.dateDelivered, p.dateReceived, p.userWhoReceived, p.userWhoDelivered, s.name AS shipperName, r.name AS receiverName, v.name AS vendorName, p.pickedUp, p.datePickedUp, p.userWhoPickedUp, p.userWhoAuthorizedPickUp
                             FROM AppBundle:Package p, AppBundle:Shipper s, AppBundle:Receiver r, AppBundle:Vendor v
                             WHERE p.dateDelivered BETWEEN :dateTimeBegin AND :dateTimeEnd
                             AND p.shipper = s.id
                             AND p.receiver = r.id
                             AND p.vendor = v.id
                             AND p.receiver = :tokenId
                             AND p.delivered = TRUE
                             ORDER BY p.dateDelivered'
                            )->setParameters(array(
                                "dateTimeBegin" => $dateTimeBeginString,
                                "dateTimeEnd" => $dateTimeEndString,
                                "tokenId" => $tokenId
                            ));

                        }

                        break;
                    // Get all packages delivered
                    case 1:
                        if ($typeOfRequestExplode[0] == "g") {
//                            $query = $em->createQuery(
//                                'SELECT DATE(p.dateDelivered) AS dd, p.userWhoDelivered AS u, COUNT(p) AS numOfPackages FROM AppBundle:Package p
//                             WHERE p.dateDelivered BETWEEN :dateTimeBegin AND :dateTimeEnd
//                             AND p.delivered = TRUE
//                             GROUP BY dd, u
//                             ORDER BY dd, u'
//                            )->setParameters(array(
//                                "dateTimeBegin" => $dateTimeBeginString,
//                                "dateTimeEnd" => $dateTimeEndString,
//                            ));
//                            $query = $em->createQuery(
//                                'SELECT DATE(p.dateDelivered) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
//                             WHERE p.dateDelivered BETWEEN :dateTimeBegin AND :dateTimeEnd
//                             AND p.delivered = TRUE
//                             GROUP BY d'
//                            )->setParameters(array(
//                                "dateTimeBegin" => $dateTimeBeginString,
//                                "dateTimeEnd" => $dateTimeEndString,
//                            ));

                            // Date difference is less than two weeks
                            if ($dateDiff < $firstCutOffForDateDiff) {
                                $query = $em->createQuery(
                                    'SELECT DATE(p.dateDelivered) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                             WHERE p.dateDelivered BETWEEN :dateTimeBegin AND :dateTimeEnd
                             AND p.delivered = TRUE
                             GROUP BY d'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                ));
                            } else if ($dateDiff < $secondCutOffForDateDiff) { // Date difference is between two weeks and three months
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateDelivered) AS y, WEEK(p.dateDelivered, 3) AS w, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateDelivered BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.delivered = TRUE
                                     GROUP BY y, w'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                ));

                            } else { // Date difference is greater than three months and one day
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateDelivered) AS y, MONTH(p.dateDelivered) AS m, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateDelivered BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.delivered = TRUE
                                     GROUP BY y, m'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                ));

                            }
                        } else if ($typeOfRequestExplode[0] == "t" || $typeOfRequestExplode[0] == "d") { // Get the packages and their details for table/download
                            $query = $em->createQuery(
                                'SELECT p.trackingNumber, p.numberOfPackages, p.delivered, p.dateDelivered, p.dateReceived, p.userWhoReceived, p.userWhoDelivered, s.name AS shipperName, r.name AS receiverName, v.name AS vendorName, p.pickedUp, p.datePickedUp, p.userWhoPickedUp, p.userWhoAuthorizedPickUp
                             FROM AppBundle:Package p, AppBundle:Shipper s, AppBundle:Receiver r, AppBundle:Vendor v
                             WHERE p.dateDelivered BETWEEN :dateTimeBegin AND :dateTimeEnd
                             AND p.shipper = s.id
                             AND p.receiver = r.id
                             AND p.vendor = v.id
                             AND p.delivered = TRUE
                             ORDER BY p.dateDelivered'
                            )->setParameters(array(
                                "dateTimeBegin" => $dateTimeBeginString,
                                "dateTimeEnd" => $dateTimeEndString,
                            ));

                        }

                        break;
                }
                break;
            case 'p':
                switch ($requestExplode[1]) {
                    case 0:
                        if ($typeOfRequestExplode[0] == "g") {
                            // Date difference is less than two weeks
                            if ($dateDiff < $firstCutOffForDateDiff) {
                                $query = $em->createQuery(
                                    'SELECT DATE(p.datePickedUp) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                             WHERE p.datePickedUp BETWEEN :dateTimeBegin AND :dateTimeEnd
                             AND p.pickedUp = TRUE
                             GROUP BY d'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                ));
                            } else if ($dateDiff < $secondCutOffForDateDiff) { // Date difference is between two weeks and three months
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.datePickedUp) AS y, WEEK(p.datePickedUp, 3) AS w, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.datePickedUp BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.pickedUp = TRUE
                                     GROUP BY y, w'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                ));

                            } else { // Date difference is greater than three months and one day
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.datePickedUp) AS y, MONTH(p.datePickedUp) AS m, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.datePickedUp BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.pickedUp = TRUE
                                     GROUP BY y, m'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                ));

                            }
                        } else if ($typeOfRequestExplode[0] == "t" || $typeOfRequestExplode[0] == "d") { // Get the packages and their details for table/download
                            $query = $em->createQuery(
                                'SELECT p.trackingNumber, p.numberOfPackages, p.delivered, p.dateDelivered, p.dateReceived, p.userWhoReceived, p.userWhoDelivered, s.name AS shipperName, r.name AS receiverName, v.name AS vendorName, p.pickedUp, p.datePickedUp, p.userWhoPickedUp, p.userWhoAuthorizedPickUp
                                 FROM AppBundle:Package p, AppBundle:Shipper s, AppBundle:Receiver r, AppBundle:Vendor v
                                 WHERE p.datePickedUp BETWEEN :dateTimeBegin AND :dateTimeEnd
                                 AND p.shipper = s.id
                                 AND p.receiver = r.id
                                 AND p.vendor = v.id
                                 AND p.pickedUp = TRUE
                                 ORDER BY p.datePickedUp'
                            )->setParameters(array(
                                "dateTimeBegin" => $dateTimeBeginString,
                                "dateTimeEnd" => $dateTimeEndString,
                            ));

                        }
                        break;
                    default:
                        break;
                }

                break;
            case 'o':
                switch ($requestExplode[1]) {
                    case 0:
                        if ($typeOfRequestExplode[0] == "g") {
                            // Date difference is less than two weeks
                            if ($dateDiff < $firstCutOffForDateDiff) {
                                $query = $em->createQuery(
                                    'SELECT DATE(p.dateReceived) AS d, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                    WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                    AND p.delivered = FALSE
                                    AND p.pickedUp = FALSE
                                    GROUP BY d'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                ));
                            } else if ($dateDiff < $secondCutOffForDateDiff) { // Date difference is between two weeks and three months
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateReceived) AS y, WEEK(p.dateReceived, 3) AS w, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.delivered = FALSE
                                     AND p.pickedUp = FALSE
                                     GROUP BY y, w'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                ));

                            } else { // Date difference is greater than three months and one day
                                $query = $em->createQuery(
                                    'SELECT YEAR(p.dateReceived) AS y, MONTH(p.dateReceived) AS m, COUNT(p) AS numOfPackages FROM AppBundle:Package p
                                     WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                     AND p.delivered = FALSE
                                     AND p.pickedUp = FALSE
                                     GROUP BY y, m'
                                )->setParameters(array(
                                    "dateTimeBegin" => $dateTimeBeginString,
                                    "dateTimeEnd" => $dateTimeEndString,
                                ));

                            }
                        } else if ($typeOfRequestExplode[0] == "t" || $typeOfRequestExplode[0] == "d") { // Get the packages and their details for table/download
                            $query = $em->createQuery(
                                'SELECT p.trackingNumber, p.numberOfPackages, p.delivered, p.dateDelivered, p.dateReceived, p.userWhoReceived, p.userWhoDelivered, s.name AS shipperName, r.name AS receiverName, v.name AS vendorName, p.pickedUp, p.datePickedUp, p.userWhoPickedUp, p.userWhoAuthorizedPickUp
                                 FROM AppBundle:Package p, AppBundle:Shipper s, AppBundle:Receiver r, AppBundle:Vendor v
                                 WHERE p.dateReceived BETWEEN :dateTimeBegin AND :dateTimeEnd
                                 AND p.shipper = s.id
                                 AND p.receiver = r.id
                                 AND p.vendor = v.id
                                 AND p.delivered = FALSE
                                 AND p.pickedUp = FALSE
                                 ORDER BY p.dateReceived'
                            )->setParameters(array(
                                "dateTimeBegin" => $dateTimeBeginString,
                                "dateTimeEnd" => $dateTimeEndString,
                            ));
                        }
                        break;
                    default;
                        break;
                }

                break;
            default:
                $results = array(
                    'result' => 'error',
                    'message' => 'Unable to determine request type',
                    'object' => NULL
                );

                return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }

        // Get the results and filter them
        $packages = $query->getResult();

        if (!empty($packages)) {
            if ((count($typeOfRequestExplode) == 2) && ($typeOfRequestExplode[1] == "xml")) {
                $xmlOutput = $this->get('serializer')->serialize($packages, 'xml');


                // Create a new response
                $response = new JsonResponse();

                // Set the response content type and content disposition
                $response->headers->set('Content-Type', 'text/xml');
                $response->headers->set('Content-Disposition', 'attachment;filename=' . $requestQuery . "-" . $typeOfRequestExplode[1] . ".xml");

                // Set the response content
                $response->setContent($xmlOutput);

                // Return the response as a download
                return $response;
            } else if ((count($typeOfRequestExplode) == 2) && ($typeOfRequestExplode[1] == "json")) {
                $jsonOutput = $this->get('serializer')->serialize($packages, 'json');

                // Create a new response
                $response = new JsonResponse();

                // Set the response content type and content disposition
                $response->headers->set('Content-Type', 'application/json');
                $response->headers->set('Content-Disposition', 'attachment;filename=' . $requestQuery . "-" . $typeOfRequestExplode[1] . ".json");

                // Set the response content
                $response->setContent($jsonOutput);

                // Return the response as a download
                return $response;
            } else if ((count($typeOfRequestExplode) == 2) && ($typeOfRequestExplode[1] == "csv")) {
                // Manually create a temp file with contents delimited by commas and send as download file
                $tmpFileName = tempnam(sys_get_temp_dir(), "csv");

                // Put the package contents in a stream to write to file/response
                $handle = fopen($tmpFileName, 'w');

                // First line of csv file for header/table
                fputcsv($handle, array('trackingNumber', 'vendor', 'shipper', 'receiver', 'numOfPackages', 'userwhoreceived', 'datereceived', 'delivered', 'datedelivered', 'userwhodelivered', 'pickedup', 'datepickedup', 'nameofwhopickedup', 'userwhoauthorizedpickup'));

                // For each package, create new line for package and write it to handle as csv
                foreach ($packages as $package) {
                    if ($package["delivered"] == TRUE) {
                        $packageToArray = array($package["trackingNumber"], $package['vendorName'], $package['shipperName'], $package['receiverName'], $package['numberOfPackages'], $package['userWhoReceived'], $package['dateReceived']->format("Y-m-d H:i:s"), $package['delivered'], $package['dateDelivered']->format("Y-m-d H:i:s"), $package['userWhoDelivered'], null, null, null, null);
                    } else if ($package["pickedUp"] == TRUE) {
                        $packageToArray = array($package["trackingNumber"], $package['vendorName'], $package['shipperName'], $package['receiverName'], $package['numberOfPackages'], $package['userWhoReceived'], $package['dateReceived']->format("Y-m-d H:i:s"), null, null, null, $package['pickedUp'], $package['datePickedUp']->format("Y-m-d H:i:s"), $package['userWhoPickedUp'], $package['userWhoAuthorizedPickUp']);
                    } else {
                        // Package not delivered AND not picked up
                        $packageToArray = array($package["trackingNumber"], $package['vendorName'], $package['shipperName'], $package['receiverName'], $package['numberOfPackages'], $package['userWhoReceived'], $package['dateReceived']->format("Y-m-d H:i:s"), null, null, null, null, null, null, null);
                    }

                    fputcsv($handle, $packageToArray);
                }

                // Close the handle
                fclose($handle);

                // Create a new response
                $response = new JsonResponse();

                // Set the response content
                $response->setContent(file_get_contents($tmpFileName));

                // Set the response content type and content disposition
                $response->headers->set('Content-Type', 'text/csv');
                $response->headers->set('Content-Disposition', 'attachment;filename=' . $requestQuery . "-" . $typeOfRequestExplode[1] . ".csv");

                // Return the response as a download
                return $response;
            }

            if ($typeOfRequestExplode[0] == "g") {
                if ($dateDiff >= $firstCutOffForDateDiff && $dateDiff < $secondCutOffForDateDiff) { // Date difference is between two weeks and three months

                    $reorganizedPackages = array();

                    foreach ($packages as $package) {

                        if ((integer)$package['w'] < 10) {
                            $package['w'] = "0" . $package['w'];
                        }

                        $beginDateGivenWeekAndYear = date("Y-m-d", strtotime($package['y'] . "W" . $package['w']));
                        $endDateGivenBeginDate = date("Y-m-d", strtotime($package['y'] . "W" . $package['w'] . " +4 days"));

                        $d = $beginDateGivenWeekAndYear . " - " . $endDateGivenBeginDate;

                        $dateAndNumOfPackages = array(
                            "d" => $d,
                            "numOfPackages" => $package["numOfPackages"]
                        );

                        array_push($reorganizedPackages, $dateAndNumOfPackages);

                    }

                    $results = array(
                        'result' => 'success',
                        'message' => 'Successfully queried database',
                        'requestedQuery' => $requestQuery,
                        'tokenId' => $tokenId,
                        'object' => $reorganizedPackages,
                        'type' => $typeOfRequest,
                    );
                } else if ($dateDiff > $secondCutOffForDateDiff) { // Date difference is greater than 3 months
                    $reorganizedPackages = array();

                    foreach ($packages as $package) {

                        if ((integer)$package['m'] < 10) {
                            $package['m'] = "0" . $package['m'];
                        }

                        $d = date("m/Y", strtotime($package['y'] . "-" . $package['m']));

                        $dateAndNumOfPackages = array(
                            "d" => $d,
                            "numOfPackages" => $package["numOfPackages"]
                        );

                        array_push($reorganizedPackages, $dateAndNumOfPackages);

                    }

                    $results = array(
                        'result' => 'success',
                        'message' => 'Successfully queried database',
                        'requestedQuery' => $requestQuery,
                        'tokenId' => $tokenId,
                        'object' => $reorganizedPackages,
                        'type' => $typeOfRequest,
                    );
                } else { // Default
                    $results = array(
                        'result' => 'success',
                        'message' => 'Successfully queried database',
                        'requestedQuery' => $requestQuery,
                        'tokenId' => $tokenId,
                        'object' => $packages,
                        'type' => $typeOfRequest,
                    );
                }
            } else {
                $results = array(
                    'result' => 'success',
                    'message' => 'Successfully queried database',
                    'requestedQuery' => $requestQuery,
                    'tokenId' => $tokenId,
                    'object' => $packages,
                    'type' => $typeOfRequest,
                );
            }


            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else if (count($packages) == 0) {
            $results = array(
                'result' => 'success',
                'message' => 'No packages found for given query',
                'requestedQuery' => $requestQuery,
                'tokenId' => $tokenId,
                'object' => NULL,
                'type' => $typeOfRequest,
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        } else {
            $results = array(
                'result' => 'error',
                'message' => 'Error querying database',
                'object' => NULL
            );

            return new JsonResponse($this->get('serializer')->serialize($results, 'json'));
        }
    }
}