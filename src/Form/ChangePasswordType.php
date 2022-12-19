<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangePasswordType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('password', PasswordType::class, [
						'label'    => 'Mot de passe actuel',
						'mapped'   => false,
						'required' => true,
				])
				->add('newPassword', PasswordType::class, [
						'label'       => 'Nouveau mot de passe',
						'mapped'      => false,
						'required'    => true,
						'constraints' => [
								new NotBlank(),
								new Length([
										'min'        => 8,
										'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
										'max'        => 60,
										'maxMessage' => 'Le mot de passe doit contenir au plus {{ limit }} caractères',
								]),
						],
				])
				->add('confirmNewPassword', PasswordType::class, [
						'label'    => 'Confirmer le nouveau mot de passe',
						'mapped'   => false,
						'required' => true,
				]);
	}

	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => User::class,
		]);
	}

}
