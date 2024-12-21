<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Article;
use App\Entity\Commande;
use App\Entity\Aprovisionnement;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer articles
        $articles = [];
        for ($i = 1; $i <= 10; $i++) {
            $article = new Article();
            $article->setNom("Article $i")
                ->setPrix(mt_rand(100, 1000) / 10) 
                ->setQteStock(mt_rand(20, 100)); 
            $manager->persist($article);
            $articles[] = $article;
        }

        // Créer clients
        $clients = [];
        for ($i = 1; $i <= 5; $i++) {
            $client = new Client();
            $client->setNom("ClientNom$i")
                ->setPrenom("ClientPrenom$i")
                ->setTelephone("77867$i$i")
                ->setAdresse("Dakar | Quartier $i | Villa $i");
            $manager->persist($client);
            $clients[] = $client;
        }

        // Créer  commandes
        foreach ($clients as $index => $client) {
            for ($j = 1; $j <= 2; $j++) {
                $commande = new Commande();
                $commande->setDate(new \DateTimeImmutable())
                    ->setClient($client)
                    ->setTotal(0,0); 

                $total = 0;

                // Ajouter  aprovisionnements 
                for ($k = 1; $k <= mt_rand(1, 3); $k++) { 
                    $article = $articles[array_rand($articles)];
                    $quantite = mt_rand(1, 5); 

                    $aprovisionnement = new Aprovisionnement();
                    $aprovisionnement->setArticle($article)
                        ->setQuantite($quantite)
                        ->setTotal($article->getPrix(), $quantite)
                        ->setCommande($commande);

                    $manager->persist($aprovisionnement);

            
                    $article->setQteStock($article->getQteStock() - $quantite);

                    
                    $total += $aprovisionnement->getTotal();
                }

                $commande->setTotal($total);
                $manager->persist($commande);
            }
        }

        $manager->flush();
    }
}
