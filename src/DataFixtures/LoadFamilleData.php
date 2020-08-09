<?php
namespace App\DataFixtures;

use App\Entity\Famille;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadFamilleData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "famille.json";
        $famillesArray = json_decode(file_get_contents($file), true);

        foreach ($famillesArray as $name => $objet) {
            $famille = new Famille();

            foreach ($objet as $key => $val) {
                $famille->{$key}($val);
            }
            $manager->persist($famille);
            $this->addReference($name, $famille);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadLieuData::class
        );
    }

}