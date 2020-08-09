<?php
namespace App\Form;

use App\Entity\Personne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use App\Form\newForm\YesNoType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Acteur;
use App\Entity\Livre;
use App\Entity\Film;
use App\Entity\Episode;
use App\Entity\Serie;

class PersonneType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['avec_username']) {
            $builder->add('username', TextType::class, array(
                'label' => 'Pseudo'
            ));
        }

        if ($options['avec_password']) {
            $builder->add('password', RepeatedType::class, array(
                'type' => PasswordType::class,
                'label' => 'Mot de passe',
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'first_options' => array(
                    'label' => 'Mot de passe'
                ),
                'second_options' => array(
                    'label' => 'Confirmation'
                )
            ));
        }

        $builder->add('prenom', TextType::class, array(
            'label' => 'Prénom'
        ))
            ->add('nom')
            ->add('email', EmailType::class)
            ->add('film_vo', YesNoType::class, array(
            'label' => 'Films'
        ))
            ->add('episode_vo', YesNoType::class, array(
            'label' => 'Episodes'
        ))
            ->add('livre_vo', YesNoType::class, array(
            'label' => 'Livres'
        ))
            ->add('acteur_favori', EntityType::class, array(
            'class' => Acteur::class,
            'choice_label' => function (Acteur $acteur) {
                return $acteur->getNomComplet();
            },
            'required' => false
        ))
            ->add('livre_favori', EntityType::class, array(
            'class' => Livre::class,
            'choice_label' => function (Livre $livre) {
                return $livre->getTitreComplet(0);
            },
            'required' => false
        ))
            ->add('film_favori', EntityType::class, array(
            'class' => Film::class,
            'choice_label' => function (Film $film) {
                return $film->getTitreComplet(0);
            },
            'required' => false
        ))
            ->add('episode_favori', EntityType::class, array(
            'class' => Episode::class,
            'choice_label' => function (Episode $episode) {
                return $episode->getTitreComplet(0);
            },
            'required' => false
        ))
            ->add('serie_favorie', EntityType::class, array(
            'class' => Serie::class,
            'choice_label' => function (Serie $serie) {
                return $serie->getTitreComplet(0);
            },
            'required' => false
        ))
            ->add('save', SubmitType::class, array(
            'label' => $options['label_submit'],
            'attr' => array(
                'class' => 'btn btn-media'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Personne::class,
            'allow_extra_fields' => true,
            'label_submit' => "S'inscrire",
            'avec_username' => false,
            'avec_password' => true
        ));
    }
}
