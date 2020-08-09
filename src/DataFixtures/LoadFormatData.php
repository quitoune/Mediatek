<?php
namespace App\DataFixtures;

use App\Entity\Format;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadFormatData extends Fixture implements ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "format.json";
        $formatsArray = json_decode(file_get_contents($file), true);

        foreach ($formatsArray as $name => $objet) {
            $format = new Format();

            foreach ($objet as $key => $val) {
                $format->{$key}($val);
            }
            $manager->persist($format);
            $this->addReference($name, $format);
        }
        $manager->flush();
    }
}