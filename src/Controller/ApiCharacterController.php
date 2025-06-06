<?php

namespace App\Controller;

use App\Entity\Character;
use App\Form\ApiCharacterForm;
use App\Repository\CharacterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use DateTime;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api-character')]
final class ApiCharacterController extends AbstractController
{    
    public function __construct(
        private HttpClientInterface $client,
    ) {
    }
    #
    # SI RIEN NE MARCHE, RETOURNER SUR LA SEQUENCE 21, PAGE 30
    #
    
    #[Route('/', name: 'api_character_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $response = $this->client->request(
            'GET',
            $this->getParameter('app.api_url') . '/characters/?size=50',
            [
                'auth_bearer' => $request->getSession()->get('token'), // Récupération du token
            ]
        );

        return $this->render('api-character/index.html.twig', [
           'characters' => $response->toArray(),
        ]);
    }

    #[Route('/new', name: 'api_character_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $character = [];
        $form = $this->createForm(ApiCharacterForm::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all()['api_character_form'];
            unset($data['_token']);
            $response = $this->client->request(
                'POST',
                $this->getParameter('app.api_url') . '/characters/',
                [
                    'auth_bearer' => $request->getSession()->get('token'),
                    'json' => $data,
                ]
            );

            return $this->redirectToRoute('api_character_show', [
                'identifier' => $response->toArray()['identifier']
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('api-character/new.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    #[Route('/{identifier}', name: 'api_character_show', methods: ['GET'])]
    public function show(Request $request, string $identifier): Response
    {
        $response = $this->client->request(
            'GET',
            $this->getParameter('app.api_url') . '/characters/' . $identifier,
            [
                'auth_bearer' => $request->getSession()->get('token'),
            ]
        );

        return $this->render('api-character/show.html.twig', [
            'character' => $response->toArray(),
        ]);
    }

    #[Route('/{identifier}/edit', name: 'api_character_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, string $identifier): Response
    {
        // Récupération du Character
        $response = $this->client->request(
            'GET',
            $this->getParameter('app.api_url') . '/characters/' . $identifier,
            [
                'auth_bearer' => $request->getSession()->get('token'),
            ]
        );
        $character = $response->toArray();

        $form = $this->createForm(ApiCharacterForm::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all()['api_character_form']; // Récupération des données du formulaire
            unset($data['_token']); // Suppression du token
            $this->client->request(
                'PUT',
                $this->getParameter('app.api_url') . '/characters/' . $identifier,
                [
                    'auth_bearer' => $request->getSession()->get('token'),
                    'json' => $data,
                ]
            );

            return $this->redirectToRoute('api_character_show', [
                'identifier' => $identifier
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('api-character/edit.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    #[Route('/{identifier}', name: 'api_character_delete', methods: ['POST'])]
    public function delete(Request $request, string $identifier): Response
    {
        if ($this->isCsrfTokenValid('delete' . $identifier, $request->request->get('_token'))) {
            $this->client->request(
                'DELETE',
                $this->getParameter('app.api_url') . '/characters/' . $identifier,
                [
                    'auth_bearer' => $request->getSession()->get('token')
                ]
            );
        }

        return $this->redirectToRoute('api_character_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/health/{health}', name: 'api_character_health', methods: ['GET'])]
    public function getByHealth(Request $request, string $health): Response
    {
        $response = $this->client->request(
            'GET',
            $this->getParameter('app.api_url') . '/characters/health/' . $health,
            [
                'auth_bearer' => $request->getSession()->get('token'), // Récupération du token
            ]
        );
        
        return $this->render('api-character/health.html.twig', [
           'characters' => $response->toArray(),
        ]);
    }
}
