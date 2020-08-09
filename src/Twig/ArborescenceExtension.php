<?php
/**
 * Génération automatique du fil d'ariane
 */
namespace App\Twig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
class ArborescenceExtension extends AbstractExtension
{
    public function getFilters()
    {
        return array();
    }
    
    public function getFunctions()
    {
        return array(
            new TwigFunction('arborescence', array(
                $this,
                'getArborescence'
            ))
        );
    }
    /**
     * Génère un fil d'ariane en fonction d'un tableau d'url
     *
     * @param array $paths
     *            tableau comprenant les clées / valeurs
     *            [home] => url home du projet
     *            [urls] => tableau d'urls sous la forme url => label
     *            [active] => page courante
     * @return string
     */
    public function getArborescence(array $paths, array $avatar = NULL)
    {
        $arbo = '<nav id="arborescence" aria-label="breadcrumb">';
        $arbo .= '<ol class="breadcrumb">';
        $arbo .= '<li class="breadcrumb-item"><a href="/" title="Home">';
        if(is_null($avatar) || !count($avatar)){
            $arbo .= '<img src="/image/appli/caroline_cartoon.png" class="avatar" />';
        } else {
            $arbo .= '<img src="/image/avatar/' . $avatar['chemin'] . '" class="avatar" />';
        }
        $arbo .= '</a></li>';
        
        if(isset($paths['urls']))
        {
            foreach ($paths['urls'] as $path => $nom) {
                $arbo .= '<li class="breadcrumb-item"><a href="' . $path . '">' . $nom . '</a></li>';
            }
        }
        $arbo .= '<li class="breadcrumb-item active">' . $paths['active'] . '</li>';
        $arbo .= '</ol>';
        $arbo .= '</nav>';
        return $arbo;
    }
}