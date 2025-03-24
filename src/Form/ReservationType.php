<?php
namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Station;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateReservation', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de réservation',
            ])



            ->add('idStationDepart', EntityType::class, [
                'class' => Station::class,
                'choice_label' => 'name',
                'label' => 'Station de départ',
                'required' => true,
            ])


            ->add('idStationArrivee', EntityType::class, [
                'class' => Station::class,
                'choice_label' => 'name',
                'label' => 'Station d\'arrivée',
                'required' => true,
            ])
            ->add('typeVelo', ChoiceType::class, [
                'label' => 'Type de Vélo',
                'choices' => [
                    'Vélo classique' => 'mechanical',
                    'Vélo électrique' => 'ebike',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Réserver',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
