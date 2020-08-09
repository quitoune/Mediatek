<?php
namespace App\DataFixtures;

use App\Entity\Acteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadActeurData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "acteur.json";
        $acteursArray = json_decode(file_get_contents($file), true);

        foreach ($acteursArray as $name => $objet) {
            $acteur = new Acteur();

            foreach ($objet as $key => $val) {

                switch ($key){
                    case 'setPhoto':
                        $val = $this->getReference($val);
                        $acteur->{$key}($val);
                        break;
                    case 'setDateNaissance':
                    case 'setDateDeces':
                        $val = new \DateTime($val);
                        $acteur->{$key}($val);
                        break;
                    default:
                        $acteur->{$key}($val);
                        break;
                }
            }
            $manager->persist($acteur);
            $this->addReference($name, $acteur);
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