<?php

namespace App\Twig;

use Twig\TwigFunction;
use App\Entity\Episode;
use App\Entity\Film;
use App\Entity\Livre;
use Twig\TwigFilter;

class OutilExtension extends ImageExtension
{
    public function getFilters(): array
    {
        return array(
            new TwigFilter('ouiNon', array(
                $this,
                'getOuiNon'
            )),
            new TwigFilter('decode', array(
                $this,
                'decode'
            ))
        );
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('select', array(
                $this,
                'getSelect'
            )),
            new TwigFunction('derniersAchats', array(
                $this,
                'afficherDerniersAchats'
            )),
            new TwigFunction('role', array(
                $this,
                'role'
            ))
        );
    }
    
    /**
     * Retourne oui ou non en fonction de la valeur
     *
     * @param int $value
     * @return string
     */
    public function getOuiNon(int $value)
    {
        return ($value ? 'Oui' : 'Non');
    }
    
    /**
     * Retourne un select à partir d'un array
     *
     * @param string $name
     * @param array $values
     * @param array $params
     * @return string
     */
    public function getSelect(string $name, array $values, array $params = array()){
        
        $opt = array();
        $opt['default'] = "";
        $opt['null'] = false;
        $opt['id'] = $name;
        $opt['class'] = "";
        $opt['multiple'] = false;
        
        foreach($params as $option => $valeur){
            if(isset($opt[$option])){
                $opt[$option] = $valeur;
            }
        }
        
        if(!is_array($opt['default'])){
            $opt['default'] = array($opt['default']);
        }
        
        $select = "<select name='" . $name . "' id='" . $opt['id'] . "'";
        
        if($opt['class']){
            $select .= " class='" . $opt['class'] . "'";
        }
        
        if($opt['multiple']){
            $select .= " multiple='multiple'";
        }
        
        $select .= ">";
        
        if($opt['null']){
            $select .= "<option value=''></option>";
        }
        
        foreach($values as $index => $value){
            if(is_array($value)){
                $select .= "<optgroup label='" . $index . "'>";
                foreach($value as $key => $label){
                    $select .= "<option value='" . $key . "'";
                    if(in_array($key, $opt['default'])){
                        $select .= " selected='selected'";
                    }
                    $select .= ">" . $label . "</option>";
                }
                $select .= "</optgroup>";
            } else {
                $select .= "<option value='" . $index . "'";
                if(in_array($index, $opt['default'])){
                    $select .= " selected='selected'";
                }
                $select .= ">" . $value . "</option>";
            }
        }
        
        $select .= "</select>";
        
        return $select;
    }
    
    /**
     * Tableau des derniers achats
     *
     * @param string $type
     * @param array $achats
     * @return string
     */
    public function afficherDerniersAchats(string $type, array $achats = array(), $doctrine, $vo)
    {
        $table = '';
        
        $nombre = count($achats);
        
        if ($nombre) {
            $table = '<div class="row">';
            foreach ($achats as $achat) {
                $table .= '<div class="col-img">';
                $table .= '<div class="img-elt">';
                $table .= '<a href="/' . $type . '/' . $achat['slug'] . '/afficher">';
                switch(strtolower($type)){
                    case 'episode':
                        $objet = $doctrine->getRepository(Episode::class)->findOneBy(array('id' => $achat['id']));
                        break;
                    case 'film':
                        $objet = $doctrine->getRepository(Film::class)->findOneBy(array('id' => $achat['id']));
                        break;
                    case 'livre':
                        $objet = $doctrine->getRepository(Livre::class)->findOneBy(array('id' => $achat['id']));
                        break;
                }
                $table .= $this->afficherImage($objet, $vo);
                $table .= '<br>' . $objet->getTitreComplet($vo);
                $table .= '</a>';
                $table .= '</div>';
                $table .= '</div>';
            }
            
            $table .= '</div>';
        } else {
            $type = ($type == 'episode' ? 'épisode' : $type);
            $table .= '<p class="text-media text-center">Aucun ' . $type . ' n\'a été acheté récemment.</p>';
        }
        
        return $table;
    }
    
    /**
     * 
     * @param array $array
     * @return string
     */
    public function role(array $array){
        return strtolower(str_replace("ROLE_", "", implode(", ", $array)));
    }
    
    /**
     * 
     * @param string $url
     */
    public function decode(string $url){
        return urldecode($url);
    }
}
