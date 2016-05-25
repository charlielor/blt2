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
        $fixturePackage = new Package("fixturePackage", 7, $this->getReference("fixtureShipper"), $this->getReference("fixtureReceiver"), $this->getReference("fixtureVendor"), "anon.");
        $fixturePackage2 = new Package("fixturePackage2", 3, $this->getReference("fixtureShipper2"), $this->getReference("fixtureReceiver2"), $this->getReference("fixtureVendor2"), "anon2.");


        $this->addReference("fixturePackage", $fixturePackage);
        $this->addReference("fixturePackage2", $fixturePackage2);

        $manager->persist($fixturePackage);
        $manager->persist($fixturePackage2);

        $manager->flush();
    }

    public function getOrder() {
        return 4;
    }

}