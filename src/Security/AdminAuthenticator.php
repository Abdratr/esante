<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class AdminAuthenticator extends AbstractLoginFormAuthenticator
{
    public const LOGIN_ROUTE = 'admin_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username');

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials(
                $request->request->get('_password')
            )
        );
    }

    public function onAuthenticationSuccess(
        Request $request,
        $token,
        string $firewallName
    ): ?RedirectResponse {

        return new RedirectResponse(
            $this->urlGenerator->generate('admin_dashboard')
        );
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}