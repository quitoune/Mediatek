<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BoutonExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return array();
    }

    public function getFunctions(): array
    {
        return array(
            new TwigFunction('ajouter', array(
                $this,
                'boutonAjouter'
            )),
            new TwigFunction('modifier', array(
                $this,
                'boutonModifier'
            )),
            new TwigFunction('supprimer', array(
                $this,
                'boutonSupprimer'
            )),
            new TwigFunction('collapse', array(
                $this,
                'boutonCollapse'
            ))
        );
    }
    
    /**
     * Bouton Ajouter
     *
     * @param string $path
     * @param string $classe
     * @param string $title
     * @return string
     */
    public function boutonAjouter(string $path, string $classe = "", string $title = "")
    {
        $bouton = '<a href="' . urldecode($path) . '" ';
        
        if($classe){
            $bouton .= 'class="' . $classe . '" ';
        }
        
        if($title){
            $bouton .= 'title="' . $title . '">';
        } else {
            $bouton .= 'title="Ajouter"';
        }
        
        $bouton .= '><span class="oi oi-plus"></span></a>';
        
        return $bouton;
    }
    
    /**
     * Bouton Modifier
     *
     * @param string $path
     * @param string $classe
     * @param string $title
     * @return string
     */
    public function boutonModifier(string $path, string $classe = "", string $title = "")
    {
        $bouton = '<a href="' . urldecode($path) . '" ';
        
        if($classe){
            $bouton .= 'class="' . $classe . '" ';
        }
        
        if($title){
            $bouton .= 'title="' . $title . '">';
        } else {
            $bouton .= 'title="Modifier"';
        }
        
        $bouton .= '><span class="oi oi-pencil"></span></a>';
        
        return $bouton;
    }
    
    /**
     * Bouton Supprimer
     *
     * @param string $path
     * @param string $classe
     * @param string $title
     * @return string
     */
    public function boutonSupprimer(string $path, string $classe = "", string $title = "")
    {
        $bouton = '<a href="' . urldecode($path) . '" ';
        
        if($classe){
            $bouton .= 'class="' . $classe . '" ';
        }
        
        if($title){
            $bouton .= 'title="' . $title . '">';
        } else {
            $bouton .= 'title="Supprimer"';
        }
        
        $bouton .= '><span class="oi oi-x"></span></a>';
        
        return $bouton;
    }
    
    /**
     * 
     * @param string $id
     * @param string $target
     * @param string $title
     * @return string
     */
    public function boutonCollapse(string $id, string $target, string $title)
    {
        $bouton  = '<div class="card-header" id="' . $id . '">';
    	$bouton .= '<button class="btn btn-link" type="button" data-toggle="collapse" ';
    	$bouton .= 'data-target="#' . $target . '" aria-expanded="false" ';
    	$bouton .= 'aria-controls="' . $target . '">';
    	$bouton .= $title;
    	$bouton .= '</button>';
    	$bouton .= '</div>';
    	
    	return $bouton;
    }
}
