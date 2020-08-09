<?php
namespace App\DataFixtures;

use App\Entity\Personne;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Lieu;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadPersonneData extends Fixture implements DependentFixtureInterface, ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "personne.json";
        $personnesArray = json_decode(file_get_contents($file), true);

        foreach ($personnesArray as $name => $objet) {
            $personne = new Personne();
            $lieu = new Lieu();

            foreach ($objet as $key => $val) {
                switch($key){
                    case 'setPassword':
                        $password = $this->encoder($personne, $val);
                        $personne->{$key}($password);
                        break;
                    case 'setLieu':
                    case 'setAvatar':
                        $lieu = $this->getReference($val);
                        $personne->{$key}($lieu);
                        break;
                    default:
                        $personne->{$key}($val);
                        break;
                }
            }
            $manager->persist($personne);
            $this->addReference($name, $personne);
        }
        $manager->flush();
    }

    private function encoder(Personne $personne, $val)
    {
        return $this->encoder->encodePassword($personne, $val);
    }

    public function getDependencies()
    {
        return array(
            LoadFamilleData::class,
            LoadAvatarData::class,
            LoadLieuData::class
        );
    }
}