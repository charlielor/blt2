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
        $fixturePackage3 = new Package("fixturePackage3", 1, $this->getReference("fixtureShipper"), $this->getReference("fixtureReceiver"), $this->getReference("fixtureVendor"), "anon3.");
        $fixturePackage4 = new Package("fixturePackage4", 1, $this->getReference("fixtureShipper"), $this->getReference("fixtureReceiver"), $this->getReference("fixtureVendor"), "anon4.");

        $fixturePackage3->setDelivered(1, "anon3.");
        $fixturePackage4->setPickedUp(1, "anon4.");

        $this->addReference("fixturePackage", $fixturePackage);
        $this->addReference("fixturePackage2", $fixturePackage2);

        $manager->persist($fixturePackage);
        $manager->persist($fixturePackage2);
        $manager->persist($fixturePackage3);
        $manager->persist($fixturePackage4);

        $manager->flush();
    }

    public function getOrder() {
        return 4;
    }

}