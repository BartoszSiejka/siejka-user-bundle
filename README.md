# siejka-user-bundle

ENGLISH [HERE](README_EN.md)

siejka-user-bundle to bundle dla Symfony 5 i PHP >= 7.4 dostarczający usługę prostej obsługi użytkownika jak rejestracja, logowanie, etc.

## Konfiguracja

Aby bundle działał prawidłowo konieczne jest zainstalowanie i skonfigurowanie bundle'a google/recaptcha oraz konfiguracja mailera.

Następnie należy stworzyć w config/packages plik reset_password.yaml 
```
symfonycasts_reset_password:
request_password_repository: Siejka\UserBundle\Repository\ResetPasswordRequestRepository
```

W pliku security.yaml dodaj:
```
encoders:
    Siejka\UserBundle\Entity\User:
        algorithm: auto
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'
    providers:
        siejka_user_provider:
            entity:
                class: Siejka\UserBundle\Entity\User
                property: email
    firewalls:
        main:
            anonymous: true
            lazy: true
            provider: siejka_user_provider
            user_checker: Siejka\UserBundle\Security\UserChecker
            guard:
                authenticators:
                    - Siejka\UserBundle\Security\LoginFormAuthenticator
            logout:
                path: logout
                target: login
            remember_me:
                secret:   '%kernel.secret%'
                lifetime: 604800 # 1 week in seconds
                path:     /
                # always_remember_me: true
                secure: true
                token_provider: 'Symfony\Bridge\Doctrine\Security\RememberMe\DoctrineTokenProvider'
```
W pliku routes.yaml zaimportuj ścieżki:
```
siejka_user_bundle:
    resource: '@SiejkaUserBundle/config/routes.yaml'
```

## Autor

* **Bartosz Siejka** - [GitHub](https://github.com/BartoszSiejka) [https://bsiejka.com](https://bsiejka.com)

## Licencja

Projekt jest objęty licencją MIT (nie wliczając użytych wtyczek, które mogą mieć inne warunki licencyjne) - sprawdź szczegóły w pliku [LICENSE.md](LICENSE.md)
