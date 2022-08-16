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
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Siejka\UserBundle\Entity\User as AppUser;

/**
 * @author Bartosz Siejka <siejka.bartosz@gmail.com>
 */
class ActivitySubscriber implements EventSubscriberInterface {

    private $em;
    private $security;
    private $tokenStorage;

    public function __construct(EntityManagerInterface $em, Security $security, TokenStorageInterface $tokenStorage) {
        $this->em = $em;
        $this->security = $security;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelController() {
        $user = $this->security->getUser();
        
        if (!$user instanceof AppUser) {
            return;
        }
        
        if ($user->isDeleted() || $user->isLocked()) {
            $this->tokenStorage->setToken(null);
            $remebermeToken = $this->em->getRepository('SiejkaUserBundle:RemembermeToken')->findOneBy(array('username' => $user->getEmail()));
            $this->em->remove($remebermeToken);
            $this->em->flush();
            return;
        }

        $user->setLastActivityAt(new \DateTime());
        $this->em->persist($user);
        $this->em->flush();
    }

    public static function getSubscribedEvents() {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

}