<?php


namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Shipper;

class LoadShipper extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    public function load(ObjectManager $manager) {
        $fixtureShipper = new Shipper("fixtureShipper", "anon.");
        $fixtureShipper2 = new Shipper("fixtureShipper2", "anon.");

        $this->addReference("fixtureShipper", $fixtureShipper);
        $this->addReference("fixtureShipper2", $fixtureShipper2);

        $manager->persist($fixtureShipper);
        $manager->persist($fixtureShipper2);

        $manager->flush();
    }

    public function getOrder() {
        return 2;
    }
}