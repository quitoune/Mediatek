<?php
namespace App\DataFixtures;

use App\Entity\Photo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadPhotoData extends Fixture implements ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "photo.json";
        $photosArray = json_decode(file_get_contents($file), true);

        foreach ($photosArray as $name => $objet) {
            $photo = new Photo();

            foreach ($objet as $key => $val) {

                $photo->{$key}($val);
            }
            $manager->persist($photo);
            $this->addReference($name, $photo);
        }
        $manager->flush();
    }
}