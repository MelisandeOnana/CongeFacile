<?php

namespace App\Service;

use App\Entity\Department;
use App\Repository\DepartmentRepository;
use Doctrine\ORM\EntityManagerInterface;

class DepartmentService
{
    public function __construct(
        private DepartmentRepository $departmentRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function createDepartment(Department $department): bool
    {
        if ($this->departmentRepository->findOneBy(['name' => $department->getName()])) {
            return false;
        }
        $this->entityManager->persist($department);
        $this->entityManager->flush();
        return true;
    }

    public function updateDepartment(Department $department): bool
    {
        $existing = $this->departmentRepository->findOneBy(['name' => $department->getName()]);
        if ($existing && $existing->getId() !== $department->getId()) {
            return false;
        }
        $this->entityManager->persist($department);
        $this->entityManager->flush();
        return true;
    }

    public function canDeleteDepartment(Department $department): bool
    {
        return count($department->getCollaborators()) === 0;
    }

    public function deleteDepartment(Department $department): void
    {
        $this->entityManager->remove($department);
        $this->entityManager->flush();
    }

    public function getPaginatedDepartments($search, $paginator, $request)
    {
        $query = $this->departmentRepository->findBySearch($search);
        return $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );
    }

    public function findDepartment($id)
    {
        return $this->departmentRepository->find($id);
    }
}