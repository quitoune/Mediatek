<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Acteur;
use App\Entity\Avatar;
use App\Entity\Categorie;
use App\Entity\Episode;
use App\Entity\Famille;
use App\Entity\Format;
use App\Entity\Film;
use App\Entity\Lieu;
use App\Entity\Livre;
use App\Entity\Nationalite;
use App\Entity\Personne;
use App\Entity\Photo;
use App\Entity\Saga;
use App\Entity\Saison;
use App\Entity\Serie;
use App\Entity\Type;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AppController extends AbstractController
{

    const MAX_RESULT = 20;

    const MAX_AJAX_RESULT = 5;
    
    /**
     * Type d'objets pour lesquels la categorie est valide
     *
     * @var array
     */
    const OBJET = array(
        0 => 'Films/Livres',
        1 => 'Livres',
        2 => 'Films'
    );

    /**
     * Liste des droits
     *
     * @var array
     */
    const DROITS = array(
        'ROLE_SUPER_ADMIN' => 'Super administratreur',
        'ROLE_ADMIN' => 'Administrateur',
        'ROLE_UTILISATEUR' => 'Utilisateur'
    );
    
    /**
     * Formats acceptés pour les images
     *
     * @var array
     */
    const FORMAT_IMAGE = array(
        'jpg',
        'jpeg',
        'png',
        'gif'
    );

    /**
     *
     * @return string
     */
    public function homeURL()
    {
        return $this->generateUrl('index');
    }

    /**
     * Affichage d'un array sous une meilleure forme
     *
     * @param array $array
     */
    public function pre(array $array)
    {
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }

    /**
     * Récupération de la personne connectée
     *
     * @return \App\Entity\Personne
     */
    public function getPersonne()
    {
        $repository = $this->getDoctrine()->getRepository(Personne::class);
        $personne = $repository->findOneBy(array(
            'id' => $this->getUser()
                ->getId()
        ));
        $personne = (is_null($personne) ? new Personne() : $personne);

        return $personne;
    }
    
    /**
     * Donne un caractère aléatoirement
     * @return string
     */
    static function getRandomString(){
        $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZacdefghijklmnopqrstuvwxyz0123456789";
        return substr($string, rand(0, 61), 1);
    }
    
    /**
     * Renvoie le type d'objet, l'ID et le nom/titre s'il existe
     * @param $objet
     * @param string $type
     * @return string
     */
    public function getIdNom($objet, string $type){
        switch(strtolower($type)){
            case 'acteur':
                if($objet->getSexe()){
                    return " de l'actrice " . $objet->getId() . ' - ' . $objet->getNomComplet();
                } else {
                    return " de l'acteur " . $objet->getId() . ' - ' . $objet->getNomComplet();
                }
                break;
            case 'avatar':
                return " de l'avatar " . $objet->getId() . " - " . $objet->getNomComplet();
                break;
            case 'categorie':
                return ' de la catégorie ' . $objet->getId() . ' - ' . $objet->getNomComplet();
                break;
            case 'episode':
                return " de l'épisode " . $objet->getId() . " - " . $objet->getTitreComplet($this->get('session')->get('user')['episode_vo']);
                break;
            case 'nationalite':
                return ' de la nationnalité ' . $objet->getId() . ' - ' . $objet->getNomComplet();
                break;
            case 'saison':
                return ' de la saison ' . $objet->getId() . ' - ' . $objet->getNomComplet($this->get('session')->get('user')['episode_vo']);
                break;
            case 'serie':
                return ' de la série ' . $objet->getId() . ' - ' . $objet->getTitreComplet($this->get('session')->get('user')['episode_vo']);
                break;
            case 'livre':
            case 'film':
                return ' du ' . $type . ' ' . $objet->getId() . ' - ' . $objet->getTitreComplet($this->get('session')->get('user')[$type . '_vo']);
                break;
            case 'personne':
                return ' du membre ' . $objet->getId() . ' - ' . $objet->getNomComplet();
                break;
            case 'saga':
            case 'photo':
            case 'famille':
                return ' de la ' . $type . ' ' . $objet->getId() . ' - ' . $objet->getNomComplet();
                break;
            default:
                return ' du ' . $type . ' ' . $objet->getId() . ' - ' . $objet->getNomComplet();
                break;
        }
    }
    
    /**
     * Créer un slug unique
     *
     * @param string $texte
     * @param string $type
     * @param string $supp
     * @return string
     */
    public function createSlug(string $texte, string $type, $supp = "", int $id = 0){
        $slug = $this->cleanTextForSlug($texte);
        
        switch(ucwords($type)){
            case 'Acteur':
                $repository = $this->getDoctrine()->getRepository(Acteur::class);
                break;
            case 'Avatar':
                $repository = $this->getDoctrine()->getRepository(Avatar::class);
                break;
            case 'Categorie':
                $repository = $this->getDoctrine()->getRepository(Categorie::class);
                break;
            case 'Episode':
                $repository = $this->getDoctrine()->getRepository(Episode::class);
                break;
            case 'Famille':
                $repository = $this->getDoctrine()->getRepository(Famille::class);
                break;
            case 'Film':
                $repository = $this->getDoctrine()->getRepository(Film::class);
                break;
            case 'Format':
                $repository = $this->getDoctrine()->getRepository(Format::class);
                break;
            case 'Lieu':
                $repository = $this->getDoctrine()->getRepository(Lieu::class);
                break;
            case 'Livre':
                $repository = $this->getDoctrine()->getRepository(Livre::class);
                break;
            case 'Nationalite':
                $repository = $this->getDoctrine()->getRepository(Nationalite::class);
                break;
            case 'Personne':
                $repository = $this->getDoctrine()->getRepository(Personne::class);
                break;
            case 'Saga':
                $repository = $this->getDoctrine()->getRepository(Saga::class);
                break;
            case 'Saison':
                $repository = $this->getDoctrine()->getRepository(Saison::class);
                break;
            case 'Serie':
                $repository = $this->getDoctrine()->getRepository(Serie::class);
                break;
            case 'Type':
                $repository = $this->getDoctrine()->getRepository(Type::class);
                break;
            default:
                return $texte;
                break;
        }
        
        $object = $repository->findOneBy(array('slug' => $slug));
        if(is_null($object) || $object->getId() == $id){
            return $slug;
        } else {
            if(is_array($supp)){
                $prefix = "";
                foreach($supp as $pref){
                    $prefix .= $pref . "-";
                    $object = $repository->findOneBy(array('slug' => $prefix . $slug));
                    if(is_null($object) || $object->getId() == $id){
                        return $prefix . $slug;
                    }
                }
            } else if($supp){
                $object = $repository->findOneBy(array('slug' => $slug . "_(" . $supp . ")"));
                if(is_null($object) || $object->getId() == $id){
                    return $slug . "_(" . $supp . ")";
                }
            }
            for($i = 1; $i <= 1000; $i++){
                $object = $repository->findOneBy(array('slug' => $slug . "_" . $i));
                if(is_null($object) || $object->getId() == $id){
                    return $slug . "_" . $i;
                }
            }
        }
        return $slug . "_0";
    }
    
    /**
     * 
     * @param string $texte
     * @return string
     */
    public function createFileName(string $texte, string $extension = ""){
        if($extension == ""){
            $extension = preg_match("#\.(jpg|jpeg|gif|png)#i", $texte);
            $texte = str_replace($extension, "", $texte);
        }
        
        $chemin = htmlentities($texte, ENT_NOQUOTES, "utf-8" );
        
        $chemin = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $chemin);
        $chemin = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $chemin);
        $chemin = preg_replace('#&[^;]+;#', '', $chemin);
        
        $chemin = str_replace(array("/", "\\", "'", "#"), '-', $chemin);
        $chemin = str_replace(array("?", ",", "(", ")",":", "[", "]", '"'), '', $chemin);
        $chemin = trim($chemin);
        $chemin = implode("_", explode(' ', $chemin));
        
        $repository = $this->getDoctrine()->getRepository(Photo::class);
        $object = $repository->findOneBy(array('chemin' => $chemin . "." . $extension));
        
        if(is_null($object)){
            return $chemin . "." . $extension;
        } else {
            for($i = 1; $i <= 1000; $i++){
                $object = $repository->findOneBy(array('chemin' => $chemin . "_" . $i . "." . $extension));
                if(is_null($object)){
                    return $chemin . "_" . $i . "." . $extension;
                }
            }
        }
        
        return $chemin . "_0" . "." . $extension;
    }
    
    /**
     * 
     * @param UploadedFile $file
     * @param string $name
     */
    function uploadFile(UploadedFile $file, string $name){
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, self::FORMAT_IMAGE)) {
            return false;
        }
        
        $fileName = $this->createFileName($name, $extension);
        
        $directory = $this->getParameter('pictures_dir') . "photo/";
        if (! file_exists($directory)) {
            mkdir($directory);
        }
        $file->move($directory, $fileName);
        
        return $fileName;
    }
    
    public function cleanTextForSlug(string $texte)
    {
        $slug = htmlentities($texte, ENT_NOQUOTES, "utf-8" );
        
        $slug = preg_replace('#&([A-za-z])(?:acute|cedil|caron|circ|grave|orn|ring|slash|th|tilde|uml);#', '\1', $slug);
        $slug = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $slug);
        $slug = preg_replace('#&[^;]+;#', '', $slug);
        
        $slug = str_replace(array("/", "\\", "'", "#"), '-', $slug);
        $slug = str_replace(array("?", ",", ":", "[", "]", '"'), '', $slug);
        $slug = trim($slug);
        $slug = implode("_", explode(' ', $slug));
        
        return $slug;
    }
    
    /**
     * Retrait des accents et des caractères spéciaux
     *
     * @param string $str
     * @param string $encoding
     * @return string
     */
    public function cleanText(string $str, string $encoding = 'utf-8')
    {
        // transformer les caractères accentués en entités HTML
        $str = htmlentities($str, ENT_NOQUOTES, $encoding);
        
        // remplacer les entités HTML pour avoir juste le premier caractères non accentués
        $str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
        
        // Remplacer les ligatures tel que : , Æ ...
        $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
        // Supprimer tout le reste
        $str = preg_replace('#&[^;]+;#', '', $str);
        
        // retrait des caractères spéciaux
        $str = preg_replace("/(\\|\^|\.|\$|\||\(|\)|\[|\]|\*|\+|\?|\{|\}|\,|\=)/", '', $str);
        $str = preg_replace("/\d/", '_', $str);
        
        return $str;
    }
    
    protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        return $this->redirect(urldecode($this->generateUrl($route, $parameters)), $status);
    }
}
