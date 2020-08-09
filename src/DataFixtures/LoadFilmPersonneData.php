<?php
namespace App\DataFixtures;

use App\Entity\FilmPersonne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadFilmPersonneData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "film_personne.json";
        $filmPersonnesArray = json_decode(file_get_contents($file), true);

        foreach ($filmPersonnesArray as $name => $objet) {
            $film_personne = new FilmPersonne();

            foreach ($objet as $key => $val) {
                switch($key){
                    case 'setFilm':
                    case 'setLieu':
                    case 'setPersonne':
                    case 'setFormat':
                        $val = $this->getReference($val);
                        break;
                    case 'setDateAchat':
                        $val = new \DateTime($val);
                }
                $film_personne->{$key}($val);
            }
            $manager->persist($film_personne);
            $this->addReference($name, $film_personne);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadLieuData::class,
            LoadFilmData::class,
            LoadFormatData::class,
            LoadPersonneData::class
        );
    }
}