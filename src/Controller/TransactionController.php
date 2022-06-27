<?php
/**
 * Transaction controller.
 */

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Form\Type\TransactionType;
use App\Repository\WalletRepository;
use App\Service\TransactionServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TransactionController.
 */
#[Route('/transaction')]
class TransactionController extends AbstractController
{
    /**
     * Transaction service.
     */
    private TransactionServiceInterface $transactionService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param TransactionServiceInterface $transactionService Transaction service
     * @param TranslatorInterface         $translator         Translator
     * @param WalletRepository            $walletRepository   Wallet Repository
     */
    public function __construct(TransactionServiceInterface $transactionService, TranslatorInterface $translator, WalletRepository $walletRepository)
    {
        $this->transactionService = $transactionService;
        $this->translator = $translator;
        $this->walletRepository = $walletRepository;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'transaction_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->transactionService->getPaginatedList(
            $request->query->getInt('page', 1),
            $this->getUser()
        );

        return $this->render('transaction/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Transaction $transaction Transaction entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'transaction_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    #[IsGranted('VIEW', subject: 'transaction')]
    public function show(Transaction $transaction): Response
    {
        return $this->render('transaction/show.html.twig', ['transaction' => $transaction]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/create', name: 'transaction_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $transaction = new Transaction();
        $transaction->setAuthor($user);
        $form = $this->createForm(
            TransactionType::class,
            $transaction,
            ['action' => $this->generateUrl('transaction_create')]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->transactionService->save($transaction);
            $wallet = $this->walletRepository->findOneById($transaction->getWallet()->getId())->setBalance(
                $transaction->getWallet()->getBalance() + $transaction->getAmount()
            );
            $this->walletRepository->save($wallet);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('transaction_index');
        }

        return $this->render('transaction/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit action.
     *
     * @param Request     $request     HTTP request
     * @param Transaction $transaction Transaction entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'transaction_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('EDIT', subject: 'transaction')]
    public function edit(Request $request, Transaction $transaction): Response
    {
        $balance = $transaction->getWallet()->getBalance();

        $form = $this->createForm(TransactionType::class, $transaction, [
            'method' => 'PUT',
            'action' => $this->generateUrl('transaction_edit', ['id' => $transaction->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->transactionService->save($transaction);

            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );
            $wallet = $this->walletRepository->findOneById($transaction->getWallet()->getId())->setBalance(
                $balance + $transaction->getAmount()
            );
            $this->walletRepository->save($wallet);

            return $this->redirectToRoute('transaction_index');
        }

        return $this->render('transaction/edit.html.twig', [
            'form' => $form->createView(),
            'transaction' => $transaction,
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request     $request     HTTP request
     * @param Transaction $transaction Transaction entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'transaction_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[IsGranted('DELETE', subject: 'transaction')]
    public function delete(Request $request, Transaction $transaction): Response
    {
        $form = $this->createForm(FormType::class, $transaction, [
            'method' => 'DELETE',
            'action' => $this->generateUrl('transaction_delete', ['id' => $transaction->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->transactionService->delete($transaction);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('transaction_index');
        }

        return $this->render('transaction/delete.html.twig', [
            'form' => $form->createView(),
            'transaction' => $transaction,
        ]);
    }
}
