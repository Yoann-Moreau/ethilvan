<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('username', TextType::class, [
						'label' => 'Nom d\'utilisateur',
				])
				->add('email', EmailType::class, [
						'label' => 'Email',
				])
				->add('roles', ChoiceType::class, [
						'label'    => 'Rôles',
						'multiple' => true,
						'choices'  => [
								'Utilisateur'   => 'ROLE_USER',
								'Ethil-Vannien' => 'ROLE_EV',
								'Admin'         => 'ROLE_ADMIN',
								'Super-Admin'   => 'ROLE_SUPER_ADMIN',
						],
						'expanded' => true,
				]);
	}

	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => User::class,
		]);
	}
}
