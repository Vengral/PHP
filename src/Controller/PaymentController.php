<?php
/**
 * Payment controller.
 */

namespace App\Controller;

use App\Entity\Payment;
use App\Form\Type\PaymentType;
use App\Service\PaymentServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PaymentController.
 */
#[Route('/payment')]
class PaymentController extends AbstractController
{
    /**
     * Payment service.
     */
    private PaymentServiceInterface $paymentService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param PaymentServiceInterface $taskService Task service
     * @param TranslatorInterface     $translator  Translator
     */
    public function __construct(PaymentServiceInterface $taskService, TranslatorInterface $translator)
    {
        $this->paymentService = $taskService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'payment_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->paymentService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render('payment/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Payment $payment Payment
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'payment_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Payment $payment): Response
    {
        return $this->render('payment/show.html.twig', ['payment' => $payment]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'payment_create',
        methods: 'GET|POST|PUT',
    )]
    public function create(Request $request): Response
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->paymentService->save($payment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('payment_index');
        }

        return $this->render(
            'payment/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Payment $payment Payment entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'payment_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Payment $payment): Response
    {
        $form = $this->createForm(PaymentType::class, $payment, [
            'method' => 'PUT',
            'action' => $this->generateUrl('payment_edit', ['id' => $payment->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->paymentService->save($payment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('payment_index');
        }

        return $this->render(
            'payment/edit.html.twig',
            [
                'form' => $form->createView(),
                'payment' => $payment,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Payment $payment Payment entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'payment_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Payment $payment): Response
    {
        if (!$this->paymentService->canBeDeleted($payment)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.payment_contains_transactions')
            );

            return $this->redirectToRoute('payment_index');
        }

        $form = $this->createForm(
            FormType::class,
            $payment,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('payment_delete', ['id' => $payment->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->paymentService->delete($payment);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('payment_index');
        }

        return $this->render(
            'payment/delete.html.twig',
            [
                'form' => $form->createView(),
                'payment' => $payment,
            ]
        );
    }
}
