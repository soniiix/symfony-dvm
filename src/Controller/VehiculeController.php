<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Entity\EquipementVehicule;
use App\Entity\Vehicule;
use App\Form\EquipementType;
use App\Form\VehiculeType;
use App\Repository\VehiculeRepository;
use App\Repository\ConducteurRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

// Utilisation d'un logger pour le débogage
use Psr\Log\LoggerInterface;

class VehiculeController extends AbstractController
{
    // Logger
    private $logger;
    private $entity_manager;
    private $repository;

    /**
     * Constructeur auquel on passe en paramètre un logger
     */
    public function __construct(LoggerInterface $logger, EntityManagerInterface $entity_manager)
    {
        $this->logger = $logger;
        $this->entity_manager = $entity_manager;
        // obtenir le Repository lié au véhicule depuis l'EntityManager
        $this->repository = $entity_manager->getRepository(Vehicule::class);
    }

    #[Route('/vehicule/lister', name: 'vehicule_lister')]
    public function lister(Request $request): Response
    {
        $liste_vehicules = $this->repository->findAllOrdered();
        $svgText = "LES VÉHICULES";

        return $this->render("vehicule/lister.html.twig", [
            'liste_vehicules' => $liste_vehicules,
            'svg_text' => $svgText
        ]);
    }

    /**
     * Supprimer un véhicule étant donné son id
     */
    #[Route('/vehicule/supprimer/{id}', name: 'vehicule_supprimer')]
    public function supprimer($id): Response
    {
        // Récupérer le vehicule par son id
        $vehicule = $this->repository->find($id);

        if (!$vehicule) {
            throw $this->createNotFoundException('Aucun véhicule avec l\'identifiant ' . $id . ' n\'a été trouvé');
        }

        // Suppression du vehicule
        $this->entity_manager->remove($vehicule);
        $this->entity_manager->flush();

        return $this->redirectToRoute('vehicule_lister');
    }

    /**
     * Supprimer tous les véhicules (debug/test)
     */
    #[Route('/vehicule/supprimer_tout', name: 'vehicule_supprimer_tout')]
    public function supprimer_tout(): Response
    {
        // Récupérer les vehicules
        $vehicules = $this->repository->findAll();

        foreach ($vehicules as $vehicule) {
            $this->entity_manager->remove($vehicule);
        }

        $this->entity_manager->flush();

        return $this->redirectToRoute('vehicule_lister');
    }

    /**
     * Ajout d'un nouveau véhicule
     */
    #[Route('/vehicule/ajouter', name: 'vehicule_ajouter')]
    public function ajouter(Request $request): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->save($vehicule, true);

            return $this->redirectToRoute('vehicule_lister');
        }

        return $this->render('vehicule/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Modifier un véhicule étant donné son id
     */
    #[Route('/vehicule/modifier/{id}', name: 'vehicule_modifier')]
    public function modifier(Request $request, int $id): Response
    {

        $vehicule = $this->repository->find($id);


        if (!$vehicule) {
            throw $this->createNotFoundException('Aucun véhicule avec l\'identifiant ' . $id . ' n\'a été trouvé');
        }

        $form = $this->createForm(VehiculeType::class, $vehicule);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->repository->save($vehicule, true);

            return $this->redirectToRoute('vehicule_lister');
        }

        return $this->render('vehicule/modifier.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Voir les détails d'un équipement
     */
    #[Route('/vehicule/voir/{id}', name: 'vehicule_voir')]
    public function voir($id, EntityManagerInterface $entity_manager): Response
    {
        // Récupérer le vehicule par son id
        $vehicule = $this->repository->find($id);

        // Récupérer les équipements associés au véhicule
        $EqVeRepository = $entity_manager->getRepository(EquipementVehicule::class);
        $equipements_vehicules = $EqVeRepository->findBy([
            'eqve_vehicule' => $vehicule
        ]);
        $equipements = [];
        foreach ($equipements_vehicules as $eqv) {
            $equipements[] = $eqv->getEqVeEquipement();
        }

        return $this->render('vehicule/voir.html.twig', [
            'vehicule' => $vehicule,
            'equipements' => $equipements
        ]);
    }

    /**
     * Ajouter un équipement pour un véhicule
     */
    #[Route('/vehicule/{id}/ajouter_equipement', name: 'vehicule_ajouter_equipement')]
    public function ajouterEquipement($id, Request $request, EntityManagerInterface $entity_manager): Response
    {
        // Récupérer le vehicule par son id
        $vehicule = $this->repository->find($id);

        $eqVeRepository = $entity_manager->getRepository(EquipementVehicule::class);
        $equipementRepository = $entity_manager->getRepository(Equipement::class);
        
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement, ['include_quantite' => true]);

        $form->handleRequest($request);
 
        if ($form->isSubmitted() && $form->isValid()) {
            $equipementRepository->save($equipement, true);

            $eqVe = new EquipementVehicule();
            $eqVe->setEqVeVehicule($vehicule);
            $eqVe->setEqVeEquipement($equipement);
            //récupérer la quantité saisie dans le formulaire
            $eqVe->setEqVeQuantite($form->get('quantite')->getData());
            $eqVeRepository->save($eqVe, true);

            return $this->redirectToRoute('vehicule_voir', ['id' => $vehicule->getVeId()]);
        }
 
        return $this->render('equipement/ajouter.html.twig', [
            'form' => $form->createView(),
            'equipement_vehicule' => true
        ]);
    }
}
