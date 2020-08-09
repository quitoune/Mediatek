<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadFamilleLieuData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    
    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "famille_lieu.json";
        $famillesArray = json_decode(file_get_contents($file), true);
        foreach ($famillesArray as $name => $objet) {
            $famille = $this->getReference($name);
            
            foreach ($objet as $val) {
                $lieu = $this->getReference($val);
                $famille->addLieux($lieu);
            }
            
            $manager->persist($famille);
        }
        
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadFamilleData::class,
            LoadLieuData::class
        );
    }
}