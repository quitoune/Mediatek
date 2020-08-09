<?php
namespace App\DataFixtures;

use App\Entity\Avatar;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

class LoadAvatarData extends Fixture implements ContainerAwareInterface
{
    private $container;
    
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $file  = str_replace("\\", "/", $this->container->getParameter('fixtures_dir'));
        $file .= "avatar.json";
        $avatarsArray = json_decode(file_get_contents($file), true);

        foreach ($avatarsArray as $name => $objet) {
            $avatar = new Avatar();

            foreach ($objet as $key => $val) {
                $avatar->{$key}($val);
            }
            $manager->persist($avatar);
            $this->addReference($name, $avatar);
        }
        $manager->flush();
    }
}