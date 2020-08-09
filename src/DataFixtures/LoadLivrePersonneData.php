<?php
namespace App\DataFixtures;

use App\Entity\LivrePersonne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadLivrePersonneData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "livre_personne.json";
        $livrePersonnesArray = json_decode(file_get_contents($file), true);

        foreach ($livrePersonnesArray as $name => $objet) {
            $livre_personne = new LivrePersonne();

            foreach ($objet as $key => $val) {
                switch($key){
                    case 'setLivre':
                    case 'setLieu':
                    case 'setPersonne':
                    case 'setFormat':
                        $val = $this->getReference($val);
                        break;
                    case 'setDateAchat':
                        $val = new \DateTime($val);
                }
                $livre_personne->{$key}($val);
            }
            $manager->persist($livre_personne);
            $this->addReference($name, $livre_personne);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadLieuData::class,
            LoadLivreData::class,
            LoadPersonneData::class
        );
    }
}