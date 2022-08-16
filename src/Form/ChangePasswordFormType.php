<?php

/*
 * This file is part of the ||application-name|| app.
 *
 * (c) Bartosz Siejka
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siejka\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * @author Bartosz Siejka <siejka.bartosz@gmail.com>
 */
class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'form.registration.passwordBlankValidation',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'form.registration.passwordMinLengthValidation',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                            'maxMessage' => 'form.registration.passwordMaxLengthValidation',
                        ]),
                        new Regex([
                            'pattern' => '/\d/',
                            'match' =>   true,
                            'message' => 'form.registration.passwordNumberValidation'
                        ]),
                        new Regex([
                            'pattern' => '/[A-Z]/',
                            'match' =>   true,
                            'message' => 'form.registration.passwordCapitalLetterValidation'
                        ]),
                        new Regex([
                            'pattern' => '/[a-z]/',
                            'match' =>   true,
                            'message' => 'form.registration.passwordSmallLetterValidation'
                        ]),
                        new Regex([
                            'pattern' => '/[\W]/',
                            'match' =>   true,
                            'message' => 'form.registration.passwordSpecialCharValidation'
                        ]),
                        new Regex([
                            'pattern' => '/[\s]/',
                            'match' =>   false,
                            'message' => 'form.registration.passwordWhiteSpaceValidation'
                        ])
                    ],
                    'label' => 'form.registration.newPassword',
                ],
                'second_options' => [
                    'label' => 'form.registration.repeatPassword',
                ],
                'invalid_message' => 'form.registration.invalidPassword',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
