<?php

namespace App\Controller;

use App\Entity\Conducteur;
use App\Entity\EquipementVehicule;
use App\Entity\Vehicule;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SyntheseController extends AbstractController
{
    #[Route('/tableau-synthetique', name: 'tableau_synthetique')]
    public function index(EntityManagerInterface $entity_manager): Response
    {
        $conducteurRepository = $entity_manager->getRepository(Conducteur::class);
        $vehiculeRepository = $entity_manager->getRepository(Vehicule::class);
        $eqVeRepository = $entity_manager->getRepository(EquipementVehicule::class);

        $tab = [];

        // récupérer chaque conducteur, par ordre alphabétique
        $conducteurs = $conducteurRepository->findBy([], ['co_nom' => 'ASC']);

        // récupérer les véhicules de chaque conducteur
        foreach($conducteurs as $conducteur){
            $vehicules = $vehiculeRepository->findBy(['ve_conducteur' => $conducteur]);
            
            $vehiculesAvecPrixEquipements = [];

            // trouver le prix total des équipements de chaque véhicule
            foreach($vehicules as $vehicule){
                $equipements_vehicules = $eqVeRepository->findBy([
                    'eqve_vehicule' => $vehicule
                ]);

                $prixEquipements = 0;
                foreach ($equipements_vehicules as $eqv) {
                    $prixEquipements += $eqv->getEqVeEquipement()->getEqPrix();
                }

                array_push($vehiculesAvecPrixEquipements, [
                    'vehicule' => $vehicule,
                    'prixEquipements' => $prixEquipements
                ]); 
            }

            array_push($tab, [
                'conducteur' => $conducteur, 
                'vehicules' => $vehiculesAvecPrixEquipements
            ]);
        }

        dump($tab);

        return $this->render('synthese/tableau.html.twig', [
            'tableauSynthetique' => $tab
        ]);
    }
}
