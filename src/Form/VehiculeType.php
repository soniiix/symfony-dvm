<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Entity\Vehicule;
use App\Entity\Conducteur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class VehiculeType extends AbstractType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ve_marque', TextType::class, [
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Marque',
                ),
                'required' => true,
            ])->add('ve_modele', TextType::class, [
                'label' => false,
                'attr' => array(
                    'placeholder' => 'Modèle',
                ),
                'required' => true,
            ])->add('ve_conducteur', EntityType::class, [
                
                'label' => false,
                'placeholder' => 'Sélectionner conducteur',
                'class' => Conducteur::class,
                'choice_label' => 'CoNom',
            ])->add('ve_date', DateType::class, [
                'label' => 'Date d\'acquisition',
                'widget' => 'single_text',
                // prevents rendering it as type="date", to avoid HTML5 date pickers
                'html5' => true,
                //'help' => 'Utiliser jj-mm-aaaa',
                //'data'   => new \DateTime()
            ]);
    }
 
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Vehicule::class
        ]);
    }
}
?>