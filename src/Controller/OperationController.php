<?php
/**
 * Operation controller.
 */

namespace App\Controller;

use App\Entity\Operation;
use App\Form\Type\OperationType;
use App\Service\OperationServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class OperationController.
 */
#[Route('/operation')]
class OperationController extends AbstractController
{
    /**
     * Operation service.
     */
    private OperationServiceInterface $operationService;

    /**
     * Translator.
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param OperationServiceInterface $taskService Task service
     * @param TranslatorInterface       $translator  Translator
     */
    public function __construct(OperationServiceInterface $taskService, TranslatorInterface $translator)
    {
        $this->operationService = $taskService;
        $this->translator = $translator;
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'operation_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->operationService->getPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render('operation/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Operation $operation Operation
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'operation_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Operation $operation): Response
    {
        return $this->render('operation/show.html.twig', ['operation' => $operation]);
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
        name: 'operation_create',
        methods: 'GET|POST|PUT',
    )]
    public function create(Request $request): Response
    {
        $operation = new Operation();
        $form = $this->createForm(OperationType::class, $operation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->operationService->save($operation);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('operation_index');
        }

        return $this->render(
            'operation/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request   $request   HTTP request
     * @param Operation $operation Operation entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'operation_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Operation $operation): Response
    {
        $form = $this->createForm(OperationType::class, $operation, [
            'method' => 'PUT',
            'action' => $this->generateUrl('operation_edit', ['id' => $operation->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->operationService->save($operation);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('operation_index');
        }

        return $this->render(
            'operation/edit.html.twig',
            [
                'form' => $form->createView(),
                'operation' => $operation,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request   $request   HTTP request
     * @param Operation $operation Operation entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'operation_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Operation $operation): Response
    {
        if (!$this->operationService->canBeDeleted($operation)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.operation_contains_transactions')
            );

            return $this->redirectToRoute('operation_index');
        }

        $form = $this->createForm(
            FormType::class,
            $operation,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('operation_delete', ['id' => $operation->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->operationService->delete($operation);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('operation_index');
        }

        return $this->render(
            'operation/delete.html.twig',
            [
                'form' => $form->createView(),
                'operation' => $operation,
            ]
        );
    }
}
