<?php

namespace Tests\AppBundle\Controller\API;


use Liip\FunctionalTestBundle\Test\WebTestCase;

class PackingSlipControllerTest extends WebTestCase {
    protected $currentDate;
    protected $dateFolder;

    // Delete folder --> From nbari at dalmp dot com http://php.net/manual/en/function.rmdir.php#110489
    private function delTree($dir) {
        $files = array_diff(scandir($dir), array('.','..'));
        foreach ($files as $file) {
            (is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
        }
        return rmdir($dir);
    }

    public function setUp() {
        $this->currentDate = new \DateTime("NOW");
        $this->dateFolder = $this->currentDate->format('Ymd');

        mkdir($this->getContainer()->get("kernel")->getRootDir() . "/../upload/" . $this->dateFolder);
        mkdir($this->getContainer()->get("kernel")->getRootDir() . "/../upload/" . $this->dateFolder . '/test');

        copy($this->getContainer()->get("kernel")->getRootDir() . "/../tests/UploadedFiles/test1.pdf", $this->getContainer()->get("kernel")->getRootDir() . "/../upload/" . $this->dateFolder . "/test/test1_copy.pdf");
        copy($this->getContainer()->get("kernel")->getRootDir() . "/../tests/UploadedFiles/test2.pdf", $this->getContainer()->get("kernel")->getRootDir() . "/../upload/" . $this->dateFolder . "/test/test2_copy.pdf");
    }

    public function tearDown() {
        $dirRoot = $this->getContainer()->get('kernel')->getRootDir() . '/../';
        $path = "upload/" . $this->dateFolder . "/";

        if (is_dir($dirRoot . $path)) {
            $this->delTree($dirRoot . $path);
        }
    }

    public function testDownloadRoute() {
        $client = static::createClient();

        $client->request("GET", '/download/' . $this->dateFolder . '/test/test1_copy.pdf');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/pdf'
            )
        );

        $client->request("GET", '/download/' . $this->dateFolder . '/test/test2_copy.pdf');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/pdf'
            )
        );
    }

    public function testPreviewRoute() {
        $client = static::createClient();

        $client->request("GET", '/preview/' . $this->dateFolder . '/test/test1_copy.pdf');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/pdf'
            )
        );

        $client->request("GET", '/preview/' . $this->dateFolder . '/test/test2_copy.pdf');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertTrue(
            $client->getResponse()->headers->contains(
                'Content-Type',
                'application/pdf'
            )
        );
    }
}