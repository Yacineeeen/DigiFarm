<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MachineRepository;
use App\Repository\MaintenanceRepository;



class ReportController extends AbstractController
{
    #[Route('/reports/maintenance-cost', name: 'reports_maintenance_cost')]
    public function maintenanceCost(MachineRepository $machineRepository): Response
    {
        $data = $machineRepository->findTotalMaintenanceCostPerMachine();

        return $this->render('report/maintenance_cost.html.twig', [
            'data' => $data,
        ]);
    }

    #[Route('/reports/maintenance-frequency', name: 'reports_maintenance_frequency')]
    public function maintenanceFrequency(MachineRepository $machineRepository): Response
    {
        $data = $machineRepository->findMaintenanceFrequency();

        return $this->render('report/maintenance_frequency.html.twig', [
            'data' => $data,
        ]);
    }

    #[Route('/reports/maintenance-cost-over-time', name: 'reports_maintenance_cost_over_time')]
    public function maintenanceCostOverTime(MaintenanceRepository $maintenanceRepository): Response
    {
        $data = $maintenanceRepository->findMaintenanceCostOverTime();

        return $this->render('report/maintenance_cost_over_time.html.twig', [
            'data' => $data,
        ]);
    }
}