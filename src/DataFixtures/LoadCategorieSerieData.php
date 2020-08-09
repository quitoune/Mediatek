<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadCategorieSerieData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "categorie_serie.json";
        $seriePersonnesArray = json_decode(file_get_contents($file), true);
        foreach ($seriePersonnesArray as $name => $objet) {
            $categorie = $this->getReference($name);
            
            foreach ($objet as $val) {
                $serie = $this->getReference($val);
                $categorie->addSerie($serie);
            }
            
            $manager->persist($categorie);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadCategorieData::class,
            LoadSerieData::class
        );
    }
}