<?php
namespace App\DataFixtures;

use App\Entity\ActeurSaison;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadActeurSaisonData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "acteur_saison.json";
        $acteurSaisonsArray = json_decode(file_get_contents($file), true);

        foreach ($acteurSaisonsArray as $name => $objet) {
            $acteur_saison = new ActeurSaison();

            foreach ($objet as $key => $val) {

                switch ($key){
                    case 'setActeur':
                    case 'setSaison':
                        $val = $this->getReference($val);
                        $acteur_saison->{$key}($val);
                        break;
                    default:
                        $acteur_saison->{$key}($val);
                        break;
                }
            }
            $manager->persist($acteur_saison);
            $this->addReference($name, $acteur_saison);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            LoadActeurData::class,
            LoadSaisonData::class
        );
    }
}