<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use App\Controller\ActeurFilmController;
use App\Controller\ActeurSaisonController;
use App\Controller\AppController;
use Twig\TwigFunction;

class FilmExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return array(
            new TwigFilter('roleFilm', array(
                $this,
                'getRoleFilm'
            )),
            new TwigFilter('roleSaison', array(
                $this,
                'getRoleSaison'
            )),
            new TwigFilter('objet', array(
                $this,
                'getObjet'
            ))
        );
    }
    
    public function getFunctions()
    {
        return array(
            new TwigFunction('titre', array(
                $this,
                'getTitre'
            ))
        );
    }
    
    /**
     * Renvoie le rôle correspondant à une valeur
     *
     * @param int $key
     * @return string
     */
    public function getRoleFilm(int $key)
    {
        return (isset(ActeurFilmController::ROLE[$key]) ? ActeurFilmController::ROLE[$key] : $key);
    }
    
    /**
     * Renvoie le rôle correspondant à une valeur
     *
     * @param int $key
     * @return string
     */
    public function getRoleSaison(int $key)
    {
        return (isset(ActeurSaisonController::ROLE[$key]) ? ActeurSaisonController::ROLE[$key] : $key);
    }
    
    /**
     * Donne le format en fonction de la valeur donnée
     *
     * @param int $key
     * @return string | int
     */
    public function getObjet(int $key)
    {
        return (isset(AppController::OBJET[$key]) ? AppController::OBJET[$key] : $key);
    }
    
    /**
     * 
     * @param Episode | Film | Livre | Serie $objet
     * @param string $vo
     * @return string
     */
    public function getTitre($objet, $vo)
    {
        $classe = (new \ReflectionClass($objet))->getShortName();
        
        if($classe == "Saison"){
            return $objet->getNomComplet($vo);
        } else {
            return $objet->getTitreComplet($vo);
        }
    }
}
