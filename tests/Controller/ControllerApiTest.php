<?php

namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ControllerApiTest extends ApiTestCase
{
    public const URL_API_LOGIN_CHECK = '/api/login_check';
    public const URL_API_ABONNEMENTS = '/api/abonnements';
    public const URL_API_UTILISATEURS = '/api/utilisateurs';

    /**
     * @throws TransportExceptionInterface
     */
    public function testAuthentificationSuccess(): void
    {

        static::createClient()->request(Request::METHOD_POST, self::URL_API_LOGIN_CHECK, [
            'body' => json_encode([
                'email' => 'jeannine.girard@dispostable.com', //assurez vous de mettre un email valide (bdd de test)
                'password' => 'Azerty123*' //assurez vous de mettre un mpd valide (bdd de test)
            ]),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testAuthentificationFailure(): void
    {
        static::createClient()->request(Request::METHOD_POST, self::URL_API_LOGIN_CHECK, [
            'body' => json_encode([
                'email' => 'jeannine.girard@dispostable.com',
                'password' => 'Azerty123'  // utilisation volontaire d'un mpd invalide
            ]),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testAccesApiSansToken(): void
    {
        static::createClient()->request(Request::METHOD_GET, self::URL_API_ABONNEMENTS, [
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @throws ServerExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|TransportExceptionInterface
     */
    public function testAccesGetAbonnements(): void
    {
        $token = $this->authentification();
        $tokenBearer = 'Bearer ' . $token;
        $reponseGetAbonnements = static::createClient()->request(Request::METHOD_GET, self::URL_API_ABONNEMENTS, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => $tokenBearer,
            ],
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK, $reponseGetAbonnements->getStatusCode());
    }

    /**
     * @throws ServerExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|TransportExceptionInterface
     */
    private function authentification(): ?string
    {
        $reponse = static::createClient()->request(Request::METHOD_POST, self::URL_API_LOGIN_CHECK, [
            'body' => json_encode([
                'email' => 'jeannine.girard@dispostable.com', //assurez vous de mettre un email valide (bdd de test)
                'password' => 'Azerty123*' //assurez vous de mettre un mpd valide (bdd de test)
            ]),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);

        return json_decode($reponse->getContent(), true)['token'] ?? null;
    }

    /**
     * @throws ServerExceptionInterface|ClientExceptionInterface|RedirectionExceptionInterface|TransportExceptionInterface
     */
    public function testAccesPostUtilisateur(): void
    {
        $token = $this->authentification();
        $body = [
            'nom' => 'dupont',
            'prenom' => 'michel',
            'email' => 'michel.dupont@dispostable.com',
            'password' => 'uigdzAIU875@jsjs-$*',
            'etablissementId' => 1,
            'roles' => 'ROLE_USER',
        ];
        $reponsePostUtilisateurs = static::createClient()->request(Request::METHOD_POST, self::URL_API_UTILISATEURS, [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
            'body' => json_encode($body),
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED, $reponsePostUtilisateurs->getStatusCode());
    }
}
