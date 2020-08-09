<?php
namespace App\DataFixtures;

use App\Entity\Serie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadSerieData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "serie.json";
        $seriesArray = json_decode(file_get_contents($file), true);

        foreach ($seriesArray as $name => $objet) {
            $serie = new Serie();

            foreach ($objet as $key => $val) {

                switch ($key){
                    case 'setSaga':
                    case 'setPhoto':
                    case 'setCategorie':
                        $val = $this->getReference($val);
                        $serie->{$key}($val);
                        break;
                    default:
                        $serie->{$key}($val);
                        break;
                }
            }
            $manager->persist($serie);
            $this->addReference($name, $serie);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadCategorieData::class,
            LoadPhotoData::class,
            LoadSagaData::class
        );
    }
}