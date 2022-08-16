# siejka-user-bundle

POLSKI [TUTAJ](README.md)

siejka-user-bundle is bundle for Symfony5 and PHP >= 7.4 provide user registration, login, etc.

## Configuration

To budnle work properly is necessary to install and configuration google/recaptcha bundle and mailer configuration.

In next step You need to create reset_password.yaml in config/packages with: 
```
symfonycasts_reset_password:
request_password_repository: Siejka\UserBundle\Repository\ResetPasswordRequestRepository
```

In security.yaml add:
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
In routes.yaml import paths:
```
siejka_user_bundle:
    resource: '@SiejkaUserBundle/config/routes.yaml'
```

## Author

* **Bartosz Siejka** - [GitHub](https://github.com/BartoszSiejka) [https://bsiejka.com](https://bsiejka.com)

## License

This project is licensed under the MIT License (exclude plugins which may have other licenses) - see the [LICENSE.md](LICENSE.md) file for details
