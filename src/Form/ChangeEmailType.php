<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChangeEmailType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('email', EmailType::class, [
						'label'       => 'Adresse email',
						'required'    => true,
						'constraints' => [
								new NotBlank(),
								new Email(),
						],
				])
				->add('password', PasswordType::class, [
						'label'    => 'Mot de passe',
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
