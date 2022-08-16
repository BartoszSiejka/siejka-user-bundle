<?php

/*
 * This file is part of the ||application-name|| app.
 *
 * (c) Bartosz Siejka
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siejka\UserBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Siejka\UserBundle\Entity\User as AppUser;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Bartosz Siejka <siejka.bartosz@gmail.com>
 */
class AuthenticationSubscriber implements EventSubscriberInterface {

    private $em;
    private $security;
    private $router;

    public function __construct(EntityManagerInterface $em, Security $security, UrlGeneratorInterface $router) {
        $this->em = $em;
        $this->security = $security;
        $this->router = $router;
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event) {
        $user = $event->getAuthenticationToken()->getUser();
        
        if (!$user instanceof AppUser) {
            return;
        }
        
        $numberOfFailedSignIn = $user->getNumberOfFailedSignIn();
        
        if ($numberOfFailedSignIn > 0) {
            $user->setNumberOfFailedSignIn(0);
        }
        
        $user->setLastLoginSuccess(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event) {
        $email = $event->getAuthenticationToken()->getCredentials()['email'];
        $user = $this->em->getRepository('SiejkaUserBundle:User')->findOneBy(array('email' => $email));
        
        if (!$user instanceof AppUser) {
            return;
        }
        
        $numberOfFailedSignIn = $user->getNumberOfFailedSignIn();
        
        if ($numberOfFailedSignIn < $_ENV['MAX_FAILED_SIGN_IN']) {
            $now = new \DateTime();
            $user->setLastLoginFailure($now);
            $user->setNumberOfFailedSignIn($numberOfFailedSignIn + 1);
            $this->em->persist($user);
            $this->em->flush($user);
        }
    }

    public static function getSubscribedEvents() {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
        ];
    }

}