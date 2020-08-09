<?php
namespace App\DataFixtures;

use App\Entity\Lieu;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadLieuData extends Fixture implements ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "lieu.json";
        $lieusArray = json_decode(file_get_contents($file), true);

        foreach ($lieusArray as $name => $objet) {
            $lieu = new Lieu();

            foreach ($objet as $key => $val) {

                $lieu->{$key}($val);
            }
            $manager->persist($lieu);
            $this->addReference($name, $lieu);
        }
        $manager->flush();
    }
}