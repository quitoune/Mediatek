<?php
namespace App\DataFixtures;

use App\Entity\FamillePersonne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadFamillePersonneData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "famille_personne.json";
        $famillePersonnesArray = json_decode(file_get_contents($file), true);

        foreach ($famillePersonnesArray as $name => $objet) {
            $famille_personne = new FamillePersonne();

            foreach ($objet as $key => $val) {

                switch ($key){
                    case 'setFamille':
                    case 'setPersonne':
                        $val = $this->getReference($val);
                        break;
                }
                $famille_personne->{$key}($val);
            }
            $manager->persist($famille_personne);
            $this->addReference($name, $famille_personne);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadFamilleData::class,
            LoadPersonneData::class
        );
    }
}