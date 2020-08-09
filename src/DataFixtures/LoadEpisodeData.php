<?php
namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadEpisodeData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "episode.json";
        $episodesArray = json_decode(file_get_contents($file), true);

        foreach ($episodesArray as $name => $objet) {
            $episode = new Episode();

            foreach ($objet as $key => $val) {
                switch($key){
                    case 'setSaison':
                        $val = $this->getReference($val);
                        break;
                    case 'setPremiereDiffusion':
                        $val = new \DateTime($val);
                        break;
                }
                $episode->{$key}($val);
            }
            $manager->persist($episode);
            $this->addReference($name, $episode);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadSaisonData::class
        );
    }
}