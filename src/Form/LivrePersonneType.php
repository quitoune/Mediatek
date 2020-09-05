<?php
namespace App\Form;

use App\Entity\LivrePersonne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Livre;
use App\Repository\LivreRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use App\Entity\Format;
use App\Repository\FormatRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Entity\Personne;
use App\Entity\Lieu;

class LivrePersonneType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('isbn', TextType::class, array(
            'label' => 'ISBN',
            'required' => false
        ));

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
                    ->andWhere('format.objet IN (0,1)')
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

        if ($options['avec_livre']) {
            if ($options['titre_vo']) {
                $builder->add('livre', EntityType::class, array(
                    'class' => Livre::class,
                    'query_builder' => function (LivreRepository $lr) {
                        return $lr->createQueryBuilder('livre')
                            ->orderBy('livre.titre_original');
                    },
                    'choice_label' => 'titre_original'
                ));
            } else {
                $builder->add('livre', EntityType::class, array(
                    'class' => Livre::class,
                    'query_builder' => function (LivreRepository $lr) {
                        return $lr->createQueryBuilder('livre')
                            ->orderBy('livre.titre');
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
            'data_class' => LivrePersonne::class,
            'label_submit' => 'Valider',
            'avec_save' => true,
            'select_personne' => array(),
            'select_lieu' => array(),
            'avec_personne' => true,
            'avec_livre' => true,
            'titre_vo' => true
        ));
    }
}
