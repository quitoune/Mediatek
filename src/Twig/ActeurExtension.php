<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Entity\Nationalite;
use App\Controller\ActeurSaisonController;
use App\Controller\ActeurFilmController;

class ActeurExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return array(
           new TwigFilter('sexe', array(
                $this,
                'getSexe'
           )),
            new TwigFilter('roleActeurFilm', array(
                $this,
                'getRoleActeurFilm'
            )),
            new TwigFilter('roleActeurSaison', array(
                $this,
                'getRoleActeurSaison'
            ))
        );
    }
    
    public function getFunctions()
    {
        return array(
            new TwigFunction('nationalite', array(
                $this,
                'getNationalite'
            )),
            new TwigFunction('select_principal', array(
                $this,
                'getSelectPricipal'
            ))
        );
    }
    
    /**
     * Retourne oui ou non en fonction de la valeur
     *
     * @param int $value
     * @return string
     */
    public function getSexe(int $value)
    {
        return ($value ? 'Féminin' : 'Masculin');
    }
    
    /**
     * Renvoie le nom masculin ou le nom féminin en fonction du sexe de l'acteur
     *
     * @param Nationalite $nationalite
     * @param bool $sexe
     * @return string
     */
    public function getNationalite(Nationalite $nationalite, bool $sexe)
    {
        if(!$sexe && !is_null($nationalite->getMasculin())){
            return $nationalite->getMasculin();
        }
        return $nationalite->getFeminin();
    }
    
    /**
     *
     * @param int $role
     * @return string
     */
    public function getRoleActeurFilm(int $role){
        switch($role){
            case 2:
                return "(principal)";
                break;
            case 1:
                return "(secondaire)";
                break;
            case 0:
                return "(caméo)";
                break;
        }
    }
    
    /**
     * renvoie un select des rôles
     * @param string $id
     * @return string
     */
    public function getSelectPricipal(string $id, string $type, string $name = "", int $principal = 2){
        if($name == ""){
            $id = $name;
        }
        
        $select = '<select id = "' . $id . '" name = "' . $name . '" class = "form-control">';
        
        if($type == "saison"){
            foreach(ActeurSaisonController::ROLE as $key => $role){
                $select .= '<option value = "' . $key . '" ' . ($principal == $key ? 'selected' : '') . ' >' . $role . '</option>';
            }
        } else {
            foreach(ActeurFilmController::ROLE as $key => $role){
                $select .= '<option value = "' . $key . '" ' . ($principal == $key ? 'selected' : '') . ' >' . $role . '</option>';
            }
        }
        $select .= '</select>';
        
        return $select;
    }
    
    /**
     *
     * @param int $role
     * @return string
     */
    public function getRoleActeurSaison(int $role){
        switch($role){
            case 2:
                return "(principal)";
                break;
            case 1:
                return "(récurrent)";
                break;
            case 0:
                return "(invité)";
                break;
        }
    }
}
