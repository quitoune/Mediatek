<?php
namespace App\DataFixtures;

use App\Entity\Type;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LoadTypeData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "type.json";
        $typesArray = json_decode(file_get_contents($file), true);

        foreach ($typesArray as $name => $objet) {
            $type = new Type();

            foreach ($objet as $key => $val) {
                $type->{$key}($val);
            }
            $manager->persist($type);
            $this->addReference($name, $type);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadPhotoData::class
        );
    }
}