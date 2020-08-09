<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadLivreCategorieData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "livre_categorie.json";
        $livrePersonnesArray = json_decode(file_get_contents($file), true);
        foreach ($livrePersonnesArray as $name => $objet) {
            $livre = $this->getReference($name);
            
            foreach ($objet as $val) {
                $categorie = $this->getReference($val);
                $livre->addCategory($categorie);
            }
            
            $manager->persist($livre);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadLivreData::class,
            LoadCategorieData::class
        );
    }
}