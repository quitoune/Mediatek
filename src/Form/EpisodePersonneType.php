<?php
namespace App\Form;

use App\Entity\EpisodePersonne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Episode;
use App\Repository\EpisodeRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Format;
use App\Repository\FormatRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\Personne;
use App\Entity\Lieu;

class EpisodePersonneType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['avec_personne']) {
            $builder->add('personne', EntityType::class, array(
                'class' => Personne::class,
                'choices' => $options['select_personne'],
                'choice_label' => function (Personne $p) {
                    if (! is_null($p->getNom()) && ! is_null($p->getPrenom())) {
                        return $p->getPrenom() . " " . $p->getNom();
                    } else {
                        return $p->getUsername();
                    }
                }
            ));
        }

        $builder->add('format', EntityType::class, array(
            'class' => Format::class,
            'query_builder' => function (FormatRepository $fr) {
                return $fr->createQueryBuilder('format')
                    ->andWhere('format.objet IN (0,2)')
                    ->orderBy('format.nom');
            },
            'choice_label' => 'nom'
        ))
            ->add('date_achat', DateType::class, array(
            'label' => "Date d'achat",
            'widget' => 'choice',
            'required' => false,
            'years' => range(date('Y') - 15, date('Y')),
            'format' => 'ddMMyyyy'
        ))
            ->add('lieu', EntityType::class, array(
            'class' => Lieu::class,
            'choices' => $options['select_lieu'],
            'choice_label' => 'nom'
        ));

        if ($options['avec_episode']) {
            if ($options['titre_vo']) {
                $builder->add('episode', EntityType::class, array(
                    'class' => Episode::class,
                    'query_builder' => function (EpisodeRepository $fr) {
                        return $fr->createQueryBuilder('episode')
                            ->orderBy('episode.titre_original');
                    },
                    'choice_label' => 'titre_original'
                ));
            } else {
                $builder->add('episode', EntityType::class, array(
                    'class' => Episode::class,
                    'query_builder' => function (EpisodeRepository $fr) {
                        return $fr->createQueryBuilder('episode')
                            ->orderBy('episode.titre');
                    },
                    'choice_label' => 'titre'
                ));
            }
        }

        if ($options['avec_save']) {
            $builder->add('save', SubmitType::class, array(
                'label' => $options['label_submit'],
                'attr' => array(
                    'class' => 'btn btn-media'
                )
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => EpisodePersonne::class,
            'label_submit' => 'Valider',
            'avec_save' => true,
            'select_personne' => array(),
            'select_lieu' => array(),
            'avec_personne' => true,
            'avec_episode' => true,
            'titre_vo' => true
        ));
    }
}
