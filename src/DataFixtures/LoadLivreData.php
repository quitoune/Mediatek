<?php
namespace App\DataFixtures;

use App\Entity\Livre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadLivreData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "livre.json";
        $livresArray = json_decode(file_get_contents($file), true);

        foreach ($livresArray as $name => $objet) {
            $livre = new Livre();

            foreach ($objet as $key => $val) {
                switch($key){
                    case 'setType':
                    case 'setPhoto':
                    case 'setSaga':
                        $val = $this->getReference($val);
                        $livre->{$key}($val);
                        break;
                    default :
                        $livre->{$key}($val);
                        break;
                }
                $livre->{$key}($val);
            }
            $manager->persist($livre);
            $this->addReference($name, $livre);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadPhotoData::class,
            LoadTypeData::class,
            LoadSagaData::class
        );
    }
}