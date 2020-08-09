<?php
namespace App\DataFixtures;

use App\Entity\EpisodePersonne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadEpisodePersonneData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "episode_personne.json";
        $episodePersonnesArray = json_decode(file_get_contents($file), true);

        foreach ($episodePersonnesArray as $name => $objet) {
            $episode_personne = new EpisodePersonne();

            foreach ($objet as $key => $val) {
                switch($key){
                    case 'setEpisode':
                    case 'setLieu':
                    case 'setPersonne':
                    case 'setFormat':
                        $val = $this->getReference($val);
                        break;
                    case 'setDateAchat':
                        $val = new \DateTime($val);
                }
                $episode_personne->{$key}($val);
            }
            $manager->persist($episode_personne);
            $this->addReference($name, $episode_personne);
        }
        $manager->flush();
    }
    
    public function getDependencies()
    {
        return array(
            LoadLieuData::class,
            LoadFormatData::class,
            LoadEpisodeData::class,
            LoadPersonneData::class
        );
    }
}