<?php
namespace App\DataFixtures;

use App\Entity\Saga;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadSagaData extends Fixture implements ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "saga.json";
        $sagasArray = json_decode(file_get_contents($file), true);

        foreach ($sagasArray as $name => $objet) {
            $saga = new Saga();

            foreach ($objet as $key => $val) {
                switch ($key){
                    case 'setSaga':
                        $val = $this->getReference($val);
                        $saga->{$key}($val);
                        break;
                    default:
                        $saga->{$key}($val);
                        break;
                }
            }
            $manager->persist($saga);
            $this->addReference($name, $saga);
        }
        $manager->flush();
    }
}