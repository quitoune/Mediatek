<?php
namespace App\Form;

use App\Entity\Acteur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use App\Form\newForm\SexeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Nationalite;
use App\Repository\NationaliteRepository;

class ActeurType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('prenom', TextType::class, array(
            'label' => 'Prénom',
            'required' => false
        ))
            ->add('nom', TextType::class, array(
            'label' => 'Nom',
            'required' => false
        ))
            ->add('nom_usage', TextType::class, array(
            'label' => "Nom d'usage",
            'required' => false
        ))
            ->add('nom_naissance', TextType::class, array(
            'label' => 'Nom de naissance',
            'required' => false
        ))
            ->add('date_naissance', BirthdayType::class, array(
            'label' => 'Date de naissance',
            'widget' => 'choice',
            'format' => 'dd/MM/yyyy',
            'required' => false
        ))
            ->add('sexe', SexeType::class)
            ->add('date_deces', BirthdayType::class, array(
            'label' => 'Date de décès',
            'widget' => 'choice',
            'format' => 'dd/MM/yyyy',
            'required' => false
        ))
            ->
        add('nationalites', EntityType::class, array(
            'class' => Nationalite::class,
            'choice_label' => 'feminin',
            'multiple' => true,
            'required' => false,
            'label' => 'Nationalité(s)',
            'attr' => array(
                'class' => 'multiple'
            ),
            'query_builder' => function (NationaliteRepository $nr) {
                return $nr->createQueryBuilder('n')
                    ->orderBy('n.pays');
            }
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
            'data_class' => Acteur::class,
            'allow_extra_fields' => true,
            'label_submit' => 'Enregistrer'
        ));
    }
}
