<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ImportDataController extends AbstractController
{
    private $doctrine;

    #[Route('/import/data', name: 'app_import_data')]
    public function index(EntityManagerInterface $entity, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Récupérer le chemin du fichier JSON
        $jsonFile = $this->getParameter('kernel.project_dir') . '/public/visiteur.json';

        // Lire le contenu du fichier JSON
        $jsonData = file_get_contents($jsonFile);

        // Tenter de décoder le JSON en tableau associatif
        $data = json_decode($jsonData, true);

        // Vérifier si le JSON a été décodé correctement
        if ($data === null) {
            return new Response('Erreur lors du décodage du JSON.', Response::HTTP_BAD_REQUEST);
        }

        // Parcourir chaque élément du tableau
        foreach ($data as $userorigin) {
            $user = new User();

            $user->setOldId((int) $userorigin['id']);
            $user->setNom($userorigin['nom']);
            $user->setPrenom($userorigin['prenom']);

            // Set login
            $user->setLogin($userorigin['login']);  // This line ensures the 'login' field is set

            // Hash the password before setting it
            $hashedPassword = $passwordHasher->hashPassword($user, $userorigin['mdp']);
            $user->setPassword($hashedPassword);

            // Generate email
            $email = strtolower($userorigin['prenom'] . '.' . $userorigin['nom'] . '@gsb.fr');
            $user->setEmail($email);

            // Set other fields
            $user->setAdresse($userorigin['adresse']);
            $user->setCp($userorigin['cp']);
            $user->setVille($userorigin['ville']);
            $user->setDateEmbauche(new \DateTime($userorigin['dateEmbauche']));

            // Set default roles
            $user->setRoles(['ROLE_USER']);

            $entity->persist($user);
        }

        // Flush after persisting all users
        $entity->flush();

        return new Response('Importation réussie', Response::HTTP_OK);
    }

    #[Route('/import/fichefrais', name: 'app_import_fichefrais')]
    public function importFicheFrais(EntityManagerInterface $entity): Response
    {
        // Récupérer le chemin du fichier JSON
        $jsonFile = $this->getParameter('kernel.project_dir') . '/public/fichefrais.json';

        // Lire le contenu du fichier JSON
        $jsonData = file_get_contents($jsonFile);

        // Tenter de décoder le JSON en tableau associatif
        $data = json_decode($jsonData, true);

        // Vérifier si le JSON a été décodé correctement
        if ($data === null) {
            return new Response('Erreur lors du décodage du JSON.', Response::HTTP_BAD_REQUEST);
        }

        // Parcourir chaque élément du tableau
        foreach ($data as $ficheFraisData) {
            $ficheFrais = new FicheFrais();

            // Default to null if no matching Etat is found
            $etat = null;
            switch ($ficheFraisData['idEtat']) {
                case 'CL':
                    $etat = $entity->getRepository(Etat::class)->find(1);
                    break;
                case 'CR':
                    $etat = $entity->getRepository(Etat::class)->find(2);
                    break;
                case 'RB':
                    $etat = $entity->getRepository(Etat::class)->find(3);
                    break;
                case 'VA':
                    $etat = $entity->getRepository(Etat::class)->find(4);
                    break;
                default:
                    // Handle missing or invalid idEtat by setting a default Etat or skipping this record
                    $etat = $entity->getRepository(Etat::class)->find(1); // Assuming 'CL' (1) is a default state
            }

            // Ensure we have a valid Etat before persisting
            if ($etat === null) {
                continue; // Skip this fiche if etat is invalid
            }

            $ficheFrais->setEtat($etat); // Set Etat

            $ficheFrais->setMois($ficheFraisData['mois']);
            $ficheFrais->setNbJustificatifs((int)$ficheFraisData['nbJustificatifs']);
            $ficheFrais->setMontantValid($ficheFraisData['montantValide']);
            $ficheFrais->setDateModif(new \DateTime($ficheFraisData['dateModif']));

            $entity->persist($ficheFrais);
        }

        // Flush after persisting all fiche frais
        $entity->flush();

        return new Response('Importation des fiches frais réussie', Response::HTTP_OK);
    }

    #[Route('/import/ligneff', name: 'app_import_ligneff')]
    public function importLigneFrais(EntityManagerInterface $entityManager): Response
    {
        $path = $this->getParameter('kernel.project_dir') . '/public/lignefraisforfait.json';
        $jsonData = file_get_contents($path);
        $data = json_decode($jsonData);

        if ($data === null) {
            throw new Exception('Failed to decode JSON data.');
        }

        foreach ($data as $item) {
            $ficheFrais = $entityManager->getRepository(FicheFrais::class)->findOneBy([
                'user' => $entityManager->getRepository(User::class)->findOneBy(['old_id' => $item->idVisiteur]),
                'mois' => $item->mois
            ]);

            if ($ficheFrais) {
                $ligneFrais = new LigneFraisForfait();
                $ligneFrais->setFicheFrais($ficheFrais);

                switch ($item->idFraisForfait) {
                    case 'ETP':
                        $ligneFrais->setFraisforfait($entityManager->getRepository(FraisForfait::class)->find(1));
                        break;
                    case 'KM':
                        $ligneFrais->setFraisforfait($entityManager->getRepository(FraisForfait::class)->find(2));
                        break;
                    case 'NUI':
                        $ligneFrais->setFraisforfait($entityManager->getRepository(FraisForfait::class)->find(3));
                        break;
                    case 'REP':
                        $ligneFrais->setFraisforfait($entityManager->getRepository(FraisForfait::class)->find(4));
                        break;
                }

                $ligneFrais->setQuantite($item->quantite);
                $entityManager->persist($ligneFrais);
            } else {
                throw new Exception('FicheFrais not found for idVisiteur: ' . $item->idVisiteur . ' and mois: ' . $item->mois);
            }
        }

        $entityManager->flush();

        return $this->render('import_data/index.html.twig', [
            'controller_name' => 'ImportDataController',
        ]);
    }

}