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

use Siejka\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

/**
 * @author Bartosz Siejka <siejka.bartosz@gmail.com>
 */
class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, array('attr' => array('maxlength' => 180, 'oninput' => 'setTimestamp("registration_form_tmstmp");')))
            ->add('emailRepeaterpt', TextType::class, array('mapped' => false, 'label' => false, 'required' => false, 'attr' => array('style' => 'display: none;', 'autocomplete' => 'nope', 'oninput' => 'setTimestamp("registration_form_tmstmp");')))
            ->add('recaptchaResponse', HiddenType::class, array('mapped' => false, 'attr' => array('data-sitekey' => '6LfGDc0aAAAAAMWAOR_W9rCoNkehUhMHTwBGgbLy')))
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'attr' => ['oninput' => 'setTimestamp("registration_form_tmstmp");'],
                'constraints' => [
                    new IsTrue([
                        'message' => 'form.registration.agreeTermsValidation',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'mapped' => false,
                'first_options' => [
                    'attr' => ['oninput' => 'setTimestamp("registration_form_tmstmp");'],
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
                    'label' => 'form.registration.password',
                ],
                'second_options' => [
                    'attr' => ['oninput' => 'setTimestamp("registration_form_tmstmp");'],
                    'label' => 'form.registration.repeatPassword',
                ],
                'invalid_message' => 'form.registration.invalidPassword',
            ])
            ->add('tmstmp', HiddenType::class, array('mapped' => false, 'label' => false, 'required' => false, 'attr' => array('autocomplete' => 'nope')))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
