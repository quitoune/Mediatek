<?php
namespace App\DataFixtures;

use App\Entity\Film;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadFilmData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "film.json";
        $filmsArray = json_decode(file_get_contents($file), true);

        foreach ($filmsArray as $name => $objet) {
            $film = new Film();

            foreach ($objet as $key => $val) {
                switch($key){
                    case 'setPhoto':
                    case 'setSaga':
                        $val = $this->getReference($val);
                        $film->{$key}($val);
                        break;
                    default :
                        $film->{$key}($val);
                        break;
                }
                
            }
            $manager->persist($film);
            $this->addReference($name, $film);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadPhotoData::class,
            LoadSagaData::class
        );
    }
}