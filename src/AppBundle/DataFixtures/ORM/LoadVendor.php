<?php


namespace AppBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Vendor;

class LoadVendor extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load(ObjectManager $manager) {
        $fixtureVendor = new Vendor("fixtureVendor", "anon.");
        $fixtureVendor2 = new Vendor("fixtureVendor2", "anon.");

        $this->addReference("fixtureVendor", $fixtureVendor);
        $this->addReference("fixtureVendor2", $fixtureVendor2);

        $manager->persist($fixtureVendor);
        $manager->persist($fixtureVendor2);

        $manager->flush();
    }

    public function getOrder() {
        return 1;
    }
}