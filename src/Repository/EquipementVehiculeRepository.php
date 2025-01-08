<?php

namespace App\Repository;

use App\Entity\EquipementVehicule;
use App\Entity\Equipement;
use App\Entity\Vehicule;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<EquipementVehicule>
 */
class EquipementVehiculeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipementVehicule::class);
    }


    public function save(EquipementVehicule $equipementVehicule, bool $flush = false): void
    {
        $this->getEntityManager()->persist($equipementVehicule);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
