<?php

namespace App\Controller\User;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
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
}
