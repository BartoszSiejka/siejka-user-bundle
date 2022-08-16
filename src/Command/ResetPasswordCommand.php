<?php

/*
 * This file is part of the ||application-name|| app.
 *
 * (c) Bartosz Siejka
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Siejka\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Console\Question\Question;

class ResetPasswordCommand extends Command
{
    protected static $defaultName = 'siejkauserbundle:reset-password';
    protected static $defaultDescription = 'Resseting password for existing user.';
    private $entityManager;
    private $userPasswordEncoderInterface;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoderInterface)
    {
        $this->entityManager = $entityManager;
        $this->userPasswordEncoderInterface = $userPasswordEncoderInterface;

        parent::__construct();
    }

    protected function configure(): void
    {
         $this->setHelp('Allow programmer to change password for user without sending email, authentication, etc.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $emailQuestion = new Question('Email: ');
        $emailQuestion->setMaxAttempts(5);
        $emailQuestion->setValidator(
                function ($value) {
                    $user = $this->entityManager->getRepository('SiejkaUserBundle:User')->findOneBy(array('email' => $value));
                    
                    if (!$user) {
                        throw new \Exception('Nie istnieje użytkownik z takim adresem email.');
                    }

                    return $user;
                });
        $passwordQuestion = new Question('Hasło: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setMaxAttempts(5);
        $passwordQuestion->setValidator(
                function ($value) {
                    if (trim($value) == '') {
                        throw new \Exception('Hasło nie może być puste.');
                    }

                    return $value;
                });

        $user = $helper->ask($input, $output, $emailQuestion);
        
        $password = $helper->ask($input, $output, $passwordQuestion);
        try {
            $encodedPassword = $this->userPasswordEncoderInterface->encodePassword(
                    $user,
                    $password
                );

            $user->setPassword($encodedPassword);
            $this->entityManager->flush();
            
            
            $output->writeln('Nowe hasło ustawione pomyślnie');
            
            return Command::SUCCESS;
        } catch (Exception $e) {
            throw new \RuntimeException('Nie udało się zmienić hasła - ' . $e);
            
            return Command::FAILURE;
        }
    }
}
