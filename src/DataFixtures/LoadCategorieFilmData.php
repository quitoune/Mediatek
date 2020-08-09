<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadCategorieFilmData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "categorie_film.json";
        $filmPersonnesArray = json_decode(file_get_contents($file), true);
        foreach ($filmPersonnesArray as $name => $objet) {
            $categorie = $this->getReference($name);
            
            foreach ($objet as $val) {
                $film = $this->getReference($val);
                $categorie->addFilm($film);
            }
            
            $manager->persist($categorie);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadCategorieData::class,
            LoadFilmData::class
        );
    }
}