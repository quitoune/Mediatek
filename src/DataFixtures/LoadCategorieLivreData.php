<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadCategorieLivreData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "categorie_livre.json";
        $livrePersonnesArray = json_decode(file_get_contents($file), true);
        foreach ($livrePersonnesArray as $name => $objet) {
            $categorie = $this->getReference($name);
            
            foreach ($objet as $val) {
                $livre = $this->getReference($val);
                $categorie->addLivre($livre);
            }
            
            $manager->persist($categorie);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadCategorieData::class,
            LoadLivreData::class
        );
    }
}