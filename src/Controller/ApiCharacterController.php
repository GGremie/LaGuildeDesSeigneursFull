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
            $this->getParameter('app.api_url') . '/characters/',
            [
                'auth_bearer' => $request->getSession()->get('token'), // Récupération du token
            ]
        );

        return $this->render('api-character/index.html.twig', [
           'characters' => $response->toArray(),
        ]);
    }

    #[Route('/new', name: 'api_character_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $character = new Character();
        $form = $this->createForm(ApiCharacterForm::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mieux dans un service
            $character->setIdentifier(hash('sha1', uniqid()));
            $character->setSlug($this->slugger->slug($character->getName())->lower());
            $character->setCreation(new DateTime());
            $character->setModification(new DateTime());

            $entityManager->persist($character);
            $entityManager->flush();

            return $this->redirectToRoute('api_character_show', [
                'id' => $character->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('api-character/new.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'api_character_show', methods: ['GET'])]
    public function show(Character $character): Response
    {
        return $this->render('api-character/show.html.twig', [
            'character' => $character,
        ]);
    }

    #[Route('/{id}/edit', name: 'api_character_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ApiCharacterForm::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mieux dans un service
            $character->setSlug($this->slugger->slug($character->getName())->lower());
            $character->setModification(new DateTime());

            $entityManager->flush();

            return $this->redirectToRoute('api_character_show', [
                'id' => $character->getId()
            ], Response::HTTP_SEE_OTHER);
        }

        return $this->render('api-character/edit.html.twig', [
            'character' => $character,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'api_character_delete', methods: ['POST'])]
    public function delete(Request $request, Character $character, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$character->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($character);
            $entityManager->flush();
        }

        return $this->redirectToRoute('api_character_index', [], Response::HTTP_SEE_OTHER);
    }
}
