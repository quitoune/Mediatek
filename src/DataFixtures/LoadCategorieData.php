<?php
namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LoadCategorieData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "categorie.json";
        $categoriesArray = json_decode(file_get_contents($file), true);

        foreach ($categoriesArray as $name => $objet) {
            $categorie = new Categorie();

            foreach ($objet as $key => $val) {
                $categorie->{$key}($val);
            }
            $manager->persist($categorie);
            $this->addReference($name, $categorie);
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