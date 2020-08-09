<?php
namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\FamillePersonne;

class LoginListener
{
    
    private $em;
    
    public function __construct(EntityManagerInterface $em, SessionInterface $session)
    {
        $this->em = $em;
        $this->session = $session;
    }
    
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        // Get the User entity.
        $user = $event->getAuthenticationToken()->getUser();
        
        if(is_null($user->getAvatar())){
            $avatar = array();
        } else {
            $avatar = array(
                'id' => $user->getAvatar()->getId(),
                'nom' => $user->getAvatar()->getNom(),
                'chemin' => $user->getAvatar()->getChemin(),
            );
        }
        
        $session = array(
            'prenom' => $user->getPrenom(),
            'nom' => $user->getNom(),
            'username' => $user->getUsername(),
            'avatar' => $avatar,
            'film_vo' => $user->getFilmVo(),
            'livre_vo' => $user->getLivreVo(),
            'episode_vo' => $user->getEpisodeVo()
        );
        
        if(!is_null($user->getAvatar())){
            $session['avatar'] = array(
                'id' => $user->getAvatar()->getId(),
                'nom' => $user->getAvatar()->getNom(),
                'chemin' => $user->getAvatar()->getChemin(),
            );
        } else {
            $session['avatar'] = array();
        }
        
        $this->session->set('user', $session);
        
        $infos = $this->em->getRepository(FamillePersonne::class)->getFamillesInfos($user->getId());
        $this->session->set('familles', $infos['familles']);
        $this->session->set('personnes', $infos['personnes']);
        $this->session->set('lieux', $infos['lieux']);
    }
}