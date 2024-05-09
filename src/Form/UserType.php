<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints as Assert;



class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mdp')
            ->add('nom')
            ->add('cin')
            ->add('salaire')
            ->add('prenom')
            ->add('email')
            ->add('numtel_user')
            ->add('adresse_user')
            ->add('agence', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Tunis' => [
                        'Tunis - Lac 1' => 'Lac 1',
                        'Tunis - Gamarth' => 'Gamarth',
                        'Tunis - La Marsa' => 'La Marsa',
                    ],
                    'Ariana' => [
                        'Ariana - Ariana Ville' => 'Ariana Ville',
                        'Ariana - Raoued' => 'Raoued',
                        'Ariana - Menzah' => 'Menzah',
                    ],
                    'Ben Arous' => [
                        'Ben Arous - Marsa' => 'Marsa',
                        'Ben Arous - Hammam Lif' => 'Hammam Lif',
                        'Ben Arous - Mohamedia' => 'Mohamedia',
                    ],
                    'Manouba' => [
                        'Manouba - Manouba' => 'Manouba',
                        'Manouba - Den Den' => 'Den Den',
                        'Manouba - Oued Ellil' => 'Oued Ellil',
                    ],
                    'Nabeul' => [
                        'Nabeul - Hammamet' => 'Hammamet',
                        'Nabeul - Nabeul Ville' => 'Nabeul Ville',
                        'Nabeul - Kelibia' => 'Kelibia',
                    ],
                    'Zaghouan' => [
                        'Zaghouan - Zaghouan' => 'Zaghouan',
                        'Zaghouan - El Fahs' => 'El Fahs',
                        'Zaghouan - Zriba' => 'Zriba',
                    ],
                    'Bizerte' => [
                        'Bizerte - Bizerte' => 'Bizerte',
                        'Bizerte - Menzel Bourguiba' => 'Menzel Bourguiba',
                        'Bizerte - Ras Jebel' => 'Ras Jebel',
                    ],
                    'Béja' => [
                        'Béja - Béja' => 'Béja',
                        'Béja - Medjez el-Bab' => 'Medjez el-Bab',
                        'Béja - Nefza' => 'Nefza',
                    ],
                    'Jendouba' => [
                        'Jendouba - Jendouba' => 'Jendouba',
                        'Jendouba - Tabarka' => 'Tabarka',
                        'Jendouba - Aïn Draham' => 'Aïn Draham',
                    ],
                    'Le Kef' => [
                        'Le Kef - Le Kef' => 'Le Kef',
                        'Le Kef - Dahmani' => 'Dahmani',
                        'Le Kef - Kalaat Senan' => 'Kalaat Senan',
                    ],
                    'Siliana' => [
                        'Siliana - Siliana' => 'Siliana',
                        'Siliana - Makthar' => 'Makthar',
                        'Siliana - Gaâfour' => 'Gaâfour',
                    ],
                    'Kairouan' => [
                        'Kairouan - Kairouan' => 'Kairouan',
                        'Kairouan - Sbikha' => 'Sbikha',
                        'Kairouan - Hajeb El Ayoun' => 'Hajeb El Ayoun',
                    ],
                    'Kasserine' => [
                        'Kasserine - Kasserine' => 'Kasserine',
                        'Kasserine - Fériana' => 'Fériana',
                        'Kasserine - Sbeitla' => 'Sbeitla',
                    ],
                    'Sidi Bouzid' => [
                        'Sidi Bouzid - Sidi Bouzid' => 'Sidi Bouzid',
                        'Sidi Bouzid - Jilma' => 'Jilma',
                        'Sidi Bouzid - Meknassy' => 'Meknassy',
                    ],
                    'Sousse' => [
                        'Sousse - Sousse' => 'Sousse',
                        'Sousse - Monastir' => 'Monastir',
                        'Sousse - Ksibet El Mediouni' => 'Ksibet El Mediouni',
                    ],
                    'Mahdia' => [
                        'Mahdia - Mahdia' => 'Mahdia',
                        'Mahdia - Ksour Essef' => 'Ksour Essef',
                        'Mahdia - Ouled Chamekh' => 'Ouled Chamekh',
                    ],
                    'Monastir' => [
                        'Monastir - Monastir' => 'Monastir',
                        'Monastir - Moknine' => 'Moknine',
                        'Monastir - Jemmal' => 'Jemmal',
                    ],
                    'Gabès' => [
                        'Gabès - Gabès' => 'Gabès',
                        'Gabès - Métouia' => 'Métouia',
                        'Gabès - Menzel Habib' => 'Menzel Habib',
                    ],
                    'Médenine' => [
                        'Médenine - Médenine' => 'Médenine',
                        'Médenine - Ben Gardane' => 'Ben Gardane',
                        'Médenine - Zarzis' => 'Zarzis',
                    ],
                    'Tataouine' => [
                        'Tataouine - Tataouine' => 'Tataouine',
                        'Tataouine - Ghomrassen' => 'Ghomrassen',
                        'Tataouine - Bir Lahmar' => 'Bir Lahmar',
                    ],
                    'Gafsa' => [
                        'Gafsa - Gafsa' => 'Gafsa',
                        'Gafsa - Métlaoui' => 'Métlaoui',
                        'Gafsa - El Ksar' => 'El Ksar',
                    ],
                    'Tozeur' => [
                    'Tozeur - Tozeur' => 'Tozeur',
                    'Tozeur - Nefta' => 'Nefta',
                    'Tozeur - Degache' => 'Degache',
                ],
                'Kebili' => [
                    'Kebili - Kebili' => 'Kebili',
                    'Kebili - Douz' => 'Douz',
                    'Kebili - Souk Lahad' => 'Souk Lahad',
                ],
                'placeholder' => 'Sélectionnez votre type',
            ]]);




    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
