<?php

/*
 * This file is part of the ||application-name|| app.
 *
 * (c) Bartosz Siejka
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siejka\UserBundle\Controller;

use Siejka\UserBundle\Entity\User;
use Siejka\UserBundle\Form\RegistrationFormType;
use Siejka\UserBundle\Security\EmailVerifier;
use Siejka\UserBundle\Security\LoginFormAuthenticator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Siejka\UserBundle\Security\FormVerifier;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Bartosz Siejka <siejka.bartosz@gmail.com>
 */
class RegistrationController extends AbstractController
{
    private $emailVerifier;
    private $translator;

    public function __construct(EmailVerifier $emailVerifier, TranslatorInterface $translator)
    {
        $this->emailVerifier = $emailVerifier;
        $this->translator = $translator;
    }

    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator): Response
    {
        $formVerifier = new FormVerifier();
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recaptchaVerification = $formVerifier->recaptchaVerifier($_SERVER["SERVER_NAME"], 'register', $form->get('recaptchaResponse')->getData(), $request->getClientIp());
            $honeyPotVerification = $formVerifier->honeyPotVerifier(array($form->get('emailRepeaterpt')->getData()));
            $timestampVerification = $formVerifier->timestampVerifier($form->get('tmstmp')->getData());
            
            if($recaptchaVerification['status'] === true && $honeyPotVerification['status'] === true && $timestampVerification['status'] === true) {
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                
                $this->emailVerifier->sendEmailConfirmation('verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('no-reply@app-name.pl', 'AppName Auto'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('@SiejkaUser/registration/confirmation_email.html.twig')
                );

                return $this->redirectToRoute('login');
            } elseif ($recaptchaVerification['status'] !== true) {
                $this->addFlash('verify_email_error', $recaptchaVerification['message']);
                
                return $this->render('@SiejkaUser/registration/register.html.twig', [
                    'registrationForm' => $form->createView()
                ]);
            } elseif ($honeyPotVerification['status'] !== true) {
                $this->addFlash('verify_email_error', $honeyPotVerification['message']);
                
                return $this->render('@SiejkaUser/registration/register.html.twig', [
                    'registrationForm' => $form->createView()
                ]);
            } elseif ($timestampVerification['status'] !== true) {
                $this->addFlash('verify_email_error', $timestampVerification['message']);
                
                return $this->render('@SiejkaUser/registration/register.html.twig', [
                    'registrationForm' => $form->createView()
                ]);
            }
        }

        return $this->render('@SiejkaUser/registration/register.html.twig', [
            'registrationForm' => $form->createView()
        ]);
    }

    public function verifyUserEmail(Request $request): Response
    {
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $id = $request->get('id'); // retrieve the user id from the url
 
        // Verify the user id exists and is not null
        if (null === $id) {
            return $this->redirectToRoute('home');
        }
 
        $user = $this->getDoctrine()->getManager()->getRepository('SiejkaUserBundle:User')->findOneBy(array('id' => $id));
 
        // Ensure the user exists in persistence
        if (!$user) {
            return $this->redirectToRoute('home');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('veryfiyng_success', $this->translator->trans('registration.emailVerification'));

        return $this->redirectToRoute('login');
    }
}
