<?php

namespace App\Controller\Admin;

use App\Entity\RobotInGroup;
use App\Message\SyncRobotMessage;
use App\Repository\PuduAccountRepository;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly PuduAccountRepository $puduAccountRepository,
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    #[Route('/admin/account-actions', name: 'admin_account_actions')]
    public function accountActions(Request $request): Response
    {
        if (null === $request->attributes->get(EA::CONTEXT_REQUEST_ATTRIBUTE)) {
            return $this->redirectToRoute('admin', ['routeName' => 'admin_account_actions']);
        }

        $accounts = $this->puduAccountRepository->findAll();

        $accountsByHost = [];
        foreach ($accounts as $account) {
            $accountsByHost[$account->getApiHost()][] = $account;
        }

        return $this->render('admin/account_actions.html.twig', [
            'accountsByHost' => $accountsByHost,
        ]);
    }

    #[Route('/admin/account-actions/{id}/sync-robots', name: 'admin_account_sync_robots', methods: ['POST'])]
    public function syncRobots(int $id, Request $request): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('sync_robots_' . $id, $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');

            return $this->redirectToRoute('admin_account_actions');
        }

        $this->bus->dispatch(new SyncRobotMessage($id));

        $this->addFlash('success', sprintf('Robot sync queued for account #%d.', $id));

        return $this->redirectToRoute('admin_account_actions');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Dashboard')
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkTo(UserCrudController::class, 'Users', 'fa fa-user');
        yield MenuItem::linkTo(PuduAccountCrudController::class, 'Pudu Accounts', 'fa fa-lock');
        yield MenuItem::linkTo(PuduAccountLogCrudController::class, 'Pudu Account Log', 'fa fa-lock');
        yield MenuItem::linkTo(RobotGroupCrudController::class, 'Robot Groups', 'fa fa-group');
        yield MenuItem::linkTo(RobotInGroupCrudController::class, 'Robots', 'fa fa-computer');
        yield MenuItem::linkToRoute('Pudu Account Actions', 'fa fa-cogs', 'admin_account_actions');


        // yield MenuItem::linkTo(SomeCrudController::class, 'The Label', 'fas fa-list');
    }
}
