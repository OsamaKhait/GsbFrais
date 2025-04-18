<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

#[Route('/import')]
class DataImportController extends AbstractController
{
    #[Route('/user', name: 'app_data_import_user')]
    public function user(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserRepository $leRepo): Response
    {
        //decode des visiteurs
        $usersjson = file_get_contents('./visiteur.json');
        $users = json_decode($usersjson, true);

        //parcour du tableau old-gsb user pour les mettre dans nouvelle GSB
        foreach ($users as $userData) {
            $user = new User();

            $user->setOldId($userData['id']);
            $user->setVille($userData['ville']);
            $user->setNom($userData['nom']);
            $user->setPrenom($userData['prenom']);
            $user->setEmail($userData['login' ]. '@gsb.fr');

            // Hash the password using UserPasswordHasherInterface
            $hashedPassword = $userPasswordHasher->hashPassword(
                $user,
                $userData['mdp'] // Assuming 'mdp' is the password field in your JSON data
            );
            $user->setPassword($hashedPassword);

            $user->setAdresse($userData['adresse']);
            $user->setCp($userData['cp']);
            $dateEmbauche = new \DateTime($userData['dateEmbauche']);
            $user->setDateEmbauche($dateEmbauche);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

    #[Route('/fiche', name: 'app_data_import_fiche')]
    public function fiche(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Decode des fiches frais
        $fichesjson = file_get_contents('./fichefrais.json');
        $fiches = json_decode($fichesjson, true);

        // Parcour du tableau old-gsb user pour les mettre dans nouvelle GSB
        foreach ($fiches as $ficheData) {
            $fiche = new FicheFrais();

            $user = $entityManager->getRepository(User::class)->findOneBy(['oldId' => $ficheData['idVisiteur']]);

            if ($user) {
                $fiche->setUser($user);

                switch ($ficheData['idEtat']) {
                    case "CL":
                        $etat = $entityManager->getRepository(Etat::class)->find(1);
                        break;
                    case "CR":
                        $etat = $entityManager->getRepository(Etat::class)->find(2);
                        break;
                    case "RB":
                        $etat = $entityManager->getRepository(Etat::class)->find(3);
                        break;
                    case "VA":
                        $etat = $entityManager->getRepository(Etat::class)->find(4);
                        break;
                    default:
                        // Log l'erreur
                        error_log("Valeur inattendue pour idEtat: " . $ficheData['idEtat']);
                        continue; // Ignorer cet enregistrement
                }

                // Vérifier si l'état a été trouvé
                if (!$etat) {
                    error_log("État non trouvé pour idEtat: " . $ficheData['idEtat']);
                    continue; // Ignorer cet enregistrement
                }

                $fiche->setEtat($etat);

                // Extraction et conversion de la date
                $date = DateTime::createFromFormat('Ym', $ficheData['mois']);
                $date -> modify('first day of this month');
                $fiche->setMois($date);

                $dateModif = new \DateTime($ficheData['dateModif']);
                $fiche->setDateModif($dateModif);
                $fiche->setMontantValid($ficheData['montantValide']);
                $fiche->setNbJustificatifs($ficheData['nbJustificatifs']);

                $entityManager->persist($fiche);
            }
        }

        $entityManager->flush();

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

    #[Route('/ligne/hors/forfait', name: 'app_data_import_lignes_hors')]
    public function lignesHorsForfait(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Decode des fiches frais
        $lignesHorsForfaitjson = file_get_contents('./lignefraishorsforfait.json');
        $lignesHors = json_decode($lignesHorsForfaitjson, true);

        foreach ($lignesHors as $ligneHorsData) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['oldId' => $ligneHorsData['idVisiteur']]);
            if (!$user) {
                error_log("Utilisateur non trouvé pour oldId: " . $ligneHorsData['idVisiteur']);
                continue;
            }

            // Conversion de la date du mois pour la recherche
            $mois = $ligneHorsData['mois'];
            $year = (int)substr($mois, 0, 4);
            $month = (int)substr($mois, 4, 2);
            $dateString = sprintf('%04d-%02d-%02d', $year, $month, 1);
            $dateMois = new \DateTimeImmutable($dateString);

            $fiche = $entityManager->getRepository(FicheFrais::class)->findOneBy([
                'user' => $user,
                'mois' => $dateMois,
            ]);

            if ($fiche) {
                $ligneHorsForfait = new LigneFraisHorsForfait();
                $ligneHorsForfait->setFicheFrais($fiche);
                $ligneHorsForfait->setLibelle($ligneHorsData['libelle']);
                $date = new \DateTime($ligneHorsData['date']);
                $ligneHorsForfait->setDate($date);
                $ligneHorsForfait->setMontant($ligneHorsData['montant']);
                $entityManager->persist($ligneHorsForfait);
            } else {
                error_log("Fiche non trouvée pour utilisateur: " . $user->getId() . " et mois: " . $dateString);
            }
        }

        $entityManager->flush();

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }

    #[Route('/ligne/forfait', name: 'app_data_import_lignes_')]
    public function lignesForfait(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Decode des fiches frais
        $lignesForfaitjson = file_get_contents('./lignefraisforfait.json');
        $lignes = json_decode($lignesForfaitjson, true);

        foreach ($lignes as $ligneData) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['oldId' => $ligneData['idVisiteur']]);
            if (!$user) {
                error_log("Utilisateur non trouvé pour oldId: " . $ligneData['idVisiteur']);
                continue;
            }

            // Conversion de la date du mois pour la recherche
            $mois = $ligneData['mois'];
            $year = (int)substr($mois, 0, 4);
            $month = (int)substr($mois, 4, 2);
            $dateString = sprintf('%04d-%02d-%02d', $year, $month, 1);
            $dateMois = new \DateTimeImmutable($dateString);

            $fiche = $entityManager->getRepository(FicheFrais::class)->findOneBy([
                'user' => $user,
                'mois' => $dateMois,
            ]);

            if ($fiche) {
                $ligneForfait = new LigneFraisForfait();
                $ligneForfait->setQuantite($ligneData['quantite']);
                $ligneForfait->setFicheFrais($fiche);

                switch($ligneData['idFraisForfait']){
                    case "ETP":
                        $ff = $entityManager->getRepository(FraisForfait::class)->find(1);
                        break;
                    case "KM":
                        $ff = $entityManager->getRepository(FraisForfait::class)->find(2);
                        break;
                    case "NUI":
                        $ff = $entityManager->getRepository(FraisForfait::class)->find(3);
                        break;
                    case "REP":
                        $ff = $entityManager->getRepository(FraisForfait::class)->find(4);
                        break;
                    default:
                        error_log("Type de frais forfait inconnu: " . $ligneData['idFraisForfait']);
                        continue;
                }

                if (!$ff) {
                    error_log("FraisForfait non trouvé pour id: " . $ligneData['idFraisForfait']);
                    continue;
                }

                $ligneForfait->setFraisForfait($ff);
                $entityManager->persist($ligneForfait);
            } else {
                error_log("Fiche non trouvée pour utilisateur: " . $user->getId() . " et mois: " . $dateString);
            }
        }

        $entityManager->flush();

        return $this->render('data_import/index.html.twig', [
            'controller_name' => 'DataImportController',
        ]);
    }
}