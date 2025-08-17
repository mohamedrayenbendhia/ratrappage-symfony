<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Redirect to dashboard for admins and super admins
        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/admin/test', name: 'app_admin_test')]
    public function test(): Response
    {
        $user = $this->getUser();
        return new Response(
            'Super Admin connecté ! Email: ' . $user->getEmail() . 
            ' - Rôles: ' . implode(', ', $user->getRoles())
        );
    }

    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function dashboard(UserRepository $userRepository): Response 
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Récupération des statistiques
        $monthlyStats = $userRepository->getMonthlyUserStats();
        $generalStats = $userRepository->getGeneralStats();

        $labels = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        // Données pour Chart.js (format JSON)
        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Registrations',
                    'data' => array_values($monthlyStats['registrations']),
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'tension' => 0.3,
                    'pointRadius' => 3,
                ],
                [
                    'label' => 'Active Users',
                    'data' => array_values($monthlyStats['actives']),
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'tension' => 0.3,
                    'pointRadius' => 3,
                ],
            ],
        ];

        $chartOptions = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'title' => [
                    'display' => true,
                    'text' => 'Monthly User Evolution (' . date('Y') . ')',
                ],
                'legend' => [
                    'position' => 'top',
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];

        return $this->render('admin/dashboard.html.twig', [
            'chartData' => json_encode($chartData),
            'chartOptions' => json_encode($chartOptions),
            'generalStats' => $generalStats,
            'estimationStats' => $userRepository->getNextMonthRegistrationEstimate(),
        ]);
    }
}
