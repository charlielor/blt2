<?php


namespace AppBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Package;

class LoadPackage extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load(ObjectManager $manager) {
        $testPackage = new Package("fixturePackage", 1, $this->getReference("fixtureShipper"), $this->getReference("fixtureReceiver"), $this->getReference("fixtureVendor"), "anon.");

        $this->addReference("fixturePackage", $testPackage);

        $manager->persist($testPackage);

        $manager->flush();
    }

    public function getOrder() {
        return 4;
    }

}