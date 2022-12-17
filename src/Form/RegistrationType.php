<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegistrationType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('username', TextType::class, [
						'label' => 'Nom d\'utilisateur',
				])
				->add('email', EmailType::class)
				->add('password', PasswordType::class, [
						'label' => 'Mot de passe',
				])
				->add('confirmPassword', PasswordType::class, [
						'label'  => 'Confirmer le mot de passe',
						'mapped' => false,
				]);
	}

	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => User::class,
		]);
	}
}
