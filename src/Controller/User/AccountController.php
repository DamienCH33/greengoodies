<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/account')]
#[IsGranted('ROLE_USER')]
final class AccountController extends AbstractController
{
    private EntityManagerInterface $em;
    private TokenStorageInterface $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    #[Route('/', name: 'app_account')]
    public function showAccount(): Response
    {
        $orders = $this->em->getRepository(Order::class)->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        return $this->render('user/account.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/remove', name: 'app_account_remove', methods: ['POST'])]
    public function removeAccount(SessionInterface $session): Response
    {
        $user = $this->getUser();

        if ($user) {
            $this->em->remove($user);
            $this->em->flush();

            $this->tokenStorage->setToken(null);
            $session->invalidate();

            $this->addFlash('success', 'Votre compte a été supprimé.');
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/api-access', name: 'app_account_api_access', methods: ['POST'])]
    public function toggleApiAccess(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException('Utilisateur non connecté.');
        }

        if (!$this->isCsrfTokenValid('toggle_api_access', $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $user->setApiAccess(!$user->isApiAccess());
        $this->em->persist($user);
        $this->em->flush();

        $message = $user->isApiAccess() ? 'API activée' : 'API désactivée';
        $this->addFlash('success', $message);

        return $this->redirectToRoute('app_account');
    }
}
