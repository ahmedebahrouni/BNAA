<?php
namespace App\Form;
use App\Entity\Credit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreditType extends AbstractType
{
public function buildForm(FormBuilderInterface $builder, array $options)
{
$builder
->add('rib', null, [
'label' => 'RIB',
'attr' => ['readonly' => true],
])
    ->add('solde', IntegerType::class, [
'label' => 'Solde',
'attr' => ['readonly' => true],
    ])
    ->add('salaire', IntegerType::class, [
        'label' => 'Salaire',
        'attr' => ['readonly' => true],
    ])
    ->add('statuscompte', null, [
        'label' => 'Status Compte',
        'attr' => ['readonly' => true],
    ])
    ->add('AMOR', IntegerType::class, [
        'label' => 'Période d\'amortissement',
    ])
    ->add('montant', IntegerType::class, [
        'label' => 'Votre Montant demandé',
    ])


// Other fields for credit information

// Add more fields as needed
->add('Save', SubmitType::class, ['label' => 'Save']);
}

public function configureOptions(OptionsResolver $resolver)
{
$resolver->setDefaults([
'data_class' => Credit::class,
]);
}
}
