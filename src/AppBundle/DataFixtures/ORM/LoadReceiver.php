<?php


namespace AppBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use AppBundle\Entity\Receiver;

class LoadReceiver extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }
    
    public function load(ObjectManager $manager) {
        $testReceiver = new Receiver("fixtureReceiver", 111, "anon.");

        $this->addReference("fixtureReceiver", $testReceiver);

        $manager->persist($testReceiver);

        $manager->flush();
    }

    public function getOrder() {
        return 3;
    }
}