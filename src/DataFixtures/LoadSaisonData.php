<?php
namespace App\DataFixtures;

use App\Entity\Saison;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadSaisonData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "saison.json";
        $saisonsArray = json_decode(file_get_contents($file), true);

        foreach ($saisonsArray as $name => $objet) {
            $saison = new Saison();

            foreach ($objet as $key => $val) {

                switch ($key){
                    case 'setPhoto':
                    case 'setSerie':
                        $val = $this->getReference($val);
                        $saison->{$key}($val);
                        break;
                    default:
                        $saison->{$key}($val);
                        break;
                }
            }
            $manager->persist($saison);
            $this->addReference($name, $saison);
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            LoadSerieData::class,
            LoadPhotoData::class
        );
    }

}