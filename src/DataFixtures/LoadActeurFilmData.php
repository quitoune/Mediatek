<?php
namespace App\DataFixtures;

use App\Entity\ActeurFilm;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadActeurFilmData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "acteur_film.json";
        $acteurFilmsArray = json_decode(file_get_contents($file), true);

        foreach ($acteurFilmsArray as $name => $objet) {
            $acteur_film = new ActeurFilm();

            foreach ($objet as $key => $val) {

                switch ($key){
                    case 'setActeur':
                    case 'setFilm':
                        $val = $this->getReference($val);
                        $acteur_film->{$key}($val);
                        break;
                    default:
                        $acteur_film->{$key}($val);
                        break;
                }
            }
            $manager->persist($acteur_film);
            $this->addReference($name, $acteur_film);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            LoadActeurData::class,
            LoadFilmData::class
        );
    }
}