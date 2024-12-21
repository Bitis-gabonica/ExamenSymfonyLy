<?php


namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Aprovisionnement;
use App\Form\CommandeType;
use App\Repository\ArticleRepository;
use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{

    #[Route('/', name: 'home')]
public function redirectToCreateCommande(): Response
{
    return $this->redirectToRoute('create.commande');
}

    #[Route('/commande/create-commande', name: 'create.commande')]
    public function createCommande(
        Request $request,
        ClientRepository $clientRepository,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Recherche du client par numéro de téléphone
        $telephone = $request->query->get('telephone');
        $client = $clientRepository->findOneBy(['telephone' => $telephone]);

        if (!$client) {
            $this->addFlash('error', 'Client introuvable.');
            return $this->render('commande/index.html.twig', [
                'form' => null,
                'client' => null,
                'articles' => null,
            ]);
        }

        // Récupérer tous les articles disponibles
        $articles = $articleRepository->findAll();

        // Création de l'objet Commande
        $commande = new Commande();
        $commande->setClient($client);

        // Création et gestion du formulaire
        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $total = 0;

            // Récupération des données des articles sélectionnés
            $aprovisionnementsData = $request->request->all('articles');
            if (!is_array($aprovisionnementsData)) {
                $aprovisionnementsData = [];
            }

            // Vérification qu'au moins un approvisionnement est valide
            $hasValidApprovisionnement = false;

            foreach ($aprovisionnementsData as $articleId => $data) {
                // Validation des données d'approvisionnement
                if (!isset($data['selected']) || !$data['selected']) {
                    continue;
                }

                if (!isset($data['quantite']) || (int)$data['quantite'] <= 0) {
                    continue;
                }

                $hasValidApprovisionnement = true;

                // Recherche de l'article
                $article = $articleRepository->find($articleId);
                if (!$article) {
                    continue;
                }

                // Création d'un nouvel approvisionnement
                $quantite = (int)$data['quantite'];
                $aprovisionnement = new Aprovisionnement();
                $aprovisionnement->setArticle($article);
                $aprovisionnement->setQuantite($quantite);
                $aprovisionnement->setTotal($article->getPrix(), $quantite);
                $aprovisionnement->setCommande($commande);

                // Ajout de l'approvisionnement à la commande
                $commande->addAprovisionnement($aprovisionnement);

                // Mise à jour du stock de l'article
                $article->setQteStock($article->getQteStock() - $quantite);

                // Ajout au total
                $total += $aprovisionnement->getTotal();

                // Persistance de l'approvisionnement
                $entityManager->persist($aprovisionnement);
            }

            // Vérification finale si au moins un approvisionnement est valide
            if (!$hasValidApprovisionnement) {
                $this->addFlash('error', 'Veuillez sélectionner au moins un article avec une quantité valide.');
                return $this->render('commande/index.html.twig', [
                    'form' => $form->createView(),
                    'client' => $client,
                    'articles' => $articles,
                ]);
            }

            // Mise à jour du total de la commande
            $commande->setTotal($total);

            // Persistance de la commande
            $entityManager->persist($commande);
            $entityManager->flush();

            // Message de succès et redirection
            $this->addFlash('success', 'Commande ajoutée avec succès !');
            return $this->redirectToRoute('details.commande', ['id' => $commande->getId()]);
        }

        // Rendu du formulaire et des articles
        return $this->render('commande/index.html.twig', [
            'form' => $form->createView(),
            'client' => $client,
            'articles' => $articles,
        ]);
    }
    


    #[Route('/commande/info_commande/{id}', name: 'details.commande')]
    public function detailsCommande(
        Request $request,
        ClientRepository $clientRepository,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager,CommandeRepository $commandeRepository
    ): Response {

        $commande=$commandeRepository->findOneBy(['id'=>$request->attributes->get('id')]);
        $client=$commande->getClient();
        $approvisionnements=$commande->getAprovisionnements();
        
       

        return $this->render('commande/detailsCommande.html.twig', [
            'commande'=>$commande,
            'client' => $client,
            'articles' => $approvisionnements,
           
        ]);
    }


    #[Route('/commande/listeCommande', name: 'commande.liste')]
    public function listeCommande(
        Request $request,
        ClientRepository $clientRepository,
        ArticleRepository $articleRepository,
        EntityManagerInterface $entityManager,CommandeRepository $commandeRepository
    ): Response {

      $commandes=$commandeRepository->findAll();
        
       

        return $this->render('commande/listeCommande.html.twig', [
            'commandes'=>$commandes,
        ]);
    }
}
