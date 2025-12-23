<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/account')]
final class AccountController extends AbstractController
{
    private EntityManagerInterface $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_account')]
    #[IsGranted('ROLE_USER')]
    public function showAccount(): Response
    {
        $order = $this->em->getRepository(Order::class)->findBy(
            ['user' => $this->getUser()],
            ['createdAt' => 'DESC']
        );

        return $this->render('user/account.html.twig', [
            'orders' => $order,
        ]);
    }
    #[Route('/remove', name: 'app_account_remove', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function removeAccount(): Response
    {
        $user = $this->getUser();

        if ($user) {
            $this->em->remove($user);
            $this->em->flush();

            $this->container->get('security.token_storage')->setToken(null);
            $this->container->get('session')->invalidate();

            $this->addFlash('success', 'Votre compte a été supprimé.');
        }

        return $this->redirectToRoute('homepage');
    }

    #[Route('/api-access', name: 'app_account_api_access', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
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

        // Inverse l'accès API
        $user->setApiAccess(!$user->isApiAccess());

        // Sauvegarde les changements
        $this->em->persist($user);
        $this->em->flush();

        if ($user->isApiAccess()) {
            $this->addFlash('success', 'API activée');
        } else {
            $this->addFlash('success', 'API désactivée');
        }

        return $this->redirectToRoute('app_account');
    }
}
