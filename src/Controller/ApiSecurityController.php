<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiSecurityController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $client
        ) {
    }

    #[Route('/api-login', name: 'api_login', methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        $response = $this->client->request(
            'POST',
            $this->getParameter('app.api_url') . '/signin', //On récupère notre paramètre
            [
                'json' => [
                    'username' => $request->request->get('_username'), // Récupération du username
                    'password' => $request->request->get('_password') // Récupération du password
                ],
            ]
        );
        // Mise en session du token
        if(200 === $response->getStatusCode()) {
            $content = json_decode($response->getContent(), true);
            $request->getSession()->set('token', $content['token']);
            return $this->redirectToRoute('api_character_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('api-security/login.html.twig');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
