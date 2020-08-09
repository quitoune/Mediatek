<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use App\Entity\Lieu;
use App\Entity\Personne;

class ImageExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return array(
            new TwigFunction('image', array(
                $this,
                'afficherImage'
            )),
            new TwigFunction('avatar', array(
                $this,
                'afficherAvatar'
            )),
            new TwigFunction('tooltip', array(
                $this,
                'tooltipImg'
            )),
            new TwigFunction('tooltip_address', array(
                $this,
                'tooltipAddress'
            ))
        );
    }
    
    /**
     * Affichage d'une image pour un élément
     * 
     * @param mixed $element
     * @param string $classe
     * @return string
     */
    public function afficherImage($element = null, int $vo = 1, string $classe = "img-fluid shadow-sm img-thumbnail")
    {
        $nom = "";
        $chemin = "";
        $folder = "photo";
        $classe = (new \ReflectionClass($element))->getShortName();
        
        switch($classe){
            case 'Photo':
                $nom = $element->getNom();
                $chemin = $element->getChemin();
                break;
            case 'Episode':
                if(is_null($element->getSaison()->getPhoto())){
                    $folder = "appli";
                    $chemin = 'default-picture.png';
                    $nom = $element->getTitreComplet($vo);
                } else {
                    $nom = $element->getSaison()->getPhoto()->getNom();
                    $chemin = $element->getSaison()->getPhoto()->getChemin();
                }
                break;
            default:
                if(is_null($element->getPhoto())){
                    $folder = "appli";
                    switch ($classe){
                        case 'Acteur':
                        case 'Personne':
                            $chemin = 'user_default_picture.gif';
                            $nom = $element->getNomComplet();
                            break;
                        case 'Film':
                        case 'Livre':
                        case 'Serie':
                            $chemin = 'default-picture.png';
                            $nom = $element->getTitreComplet($vo);
                            break;
                        case 'Saison':
                            $chemin = 'default-picture.png';
                            $nom = $element->getNomComplet($vo);
                            break;
                        default:
                            $chemin = 'default-picture.png';
                            $nom = $element->getNomComplet();
                            break;
                    }
                } else {
                    $chemin = $element->getPhoto()->getChemin();
                    switch ($classe){
                        case 'Film':
                        case 'Livre':
                        case 'Serie':
                            $nom = $element->getTitreComplet($vo);
                            break;
                        case 'Saison':
                            $nom = $element->getNomComplet($vo);
                            break;
                        default:
                            $nom = $element->getNomComplet();
                            break;
                    }
                }
                break;
        }
        $nom = str_replace('"', "'", $nom);
        
        if (preg_match("/^(http|https)/", $chemin)) {
            return '<img src="' . $chemin . '" class="' . $classe . '" />';
        }
        return '<img src="/image/' . $folder . '/' . $chemin . '" title ="' . $nom . '" class="' . $classe . '" />';
    }
    
    /**
     * Affichage d'une image pour un élément
     *
     * @param mixed $element
     * @param string $classe
     * @return string
     */
    public function afficherAvatar(Personne $personne, string $classe = "img-fluid shadow-sm img-thumbnail")
    {
        $nom = "";
        $chemin = "";
        $folder = "avatar";
        
        if(is_null($personne->getAvatar())){
            $folder = "appli";
            $chemin = 'default-picture.png';
            $nom = $personne->getNomComplet();
        } else {
            $chemin = $personne->getAvatar()->getChemin();
            $nom = $personne->getAvatar()->getNom();
        }
        $nom = str_replace('"', "'", $nom);
        
        return '<img src="/image/' . $folder . '/' . $chemin . '" title ="' . $nom . '" class="' . $classe . '" />';
    }
    
    /**
     *
     * @param $objet
     * @param string $path
     * @return string
     */
    public function tooltipImg ($objet, $path = ""){
        $tooltip  = '<a href="' . $path . '" class="tooltips">';
        
        $classe = (new \ReflectionClass($objet))->getShortName();
        switch($classe){
            case 'Acteur':
                $tooltip .= $objet->getNomComplet();
                break;
        }
        if(method_exists($objet, "getPhoto") ){
            $tooltip .= '<span class="tooltipimg">';
            $tooltip .= '<img src="/image/photo/' . $objet->getPhoto()->getChemin() . '">';
            $tooltip .= '</span>';
        }
        $tooltip .= '</a>';
        return $tooltip;
    }
    
    /**
     *
     * @param Lieu $lieu
     * @param string $path
     * @return string
     */
    public function tooltipAddress (Lieu $lieu){
        $tooltip  = '<div class="tooltips">';
        $tooltip .= $lieu->getNom();
        
        $tooltip .= '<span class="tooltipimg">';
        $tooltip .= $lieu->getAdresse();
        $tooltip .= '</span>';
        
        $tooltip .= '</div>';
        return $tooltip;
    }
}
