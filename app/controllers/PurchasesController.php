<?php

namespace app\controllers;

use
    tachyon\Request,
    app\entities\Purchase,
    app\models\Settings,
    app\repositories\ArticlesRepository,
    app\repositories\ClientsRepository
;

/**
 * Контроллер закупок
 *
 * @author Андрей Сердюк
 * @copyright (c) 2020 IMND
 */
class PurchasesController extends HasRowsController
{
    /**
     * Главная страница, список договоров.
     *
     * @param Purchase $entity
     * @param ClientsRepository $clientRepository
     * @param null $type
     */
    public function index(
        Purchase $entity,
        ClientsRepository $clientRepository,
        $type = null
    )
    {
        $this->doIndex($entity, [
            'type' => $type,
            'clients' => $clientRepository->getAllSelectList()
        ]);
    }

    /**
     * Собираем закупку за определенное число
     * @param ArticlesRepository $articleRepository
     * @param ClientsRepository $clientRepository
     */
    public function create(
        ArticlesRepository $articleRepository,
        ClientsRepository $clientRepository
    )
    {
        $entity = $this->repository->create();
        $entity->setDate(Request::getGet('date') ?? date('Y-m-d'));
        if ($this->saveEntity($entity)) {
            $this->redirect("/{$this->id}");
        }
        $row = $this->rowRepository->create(false);
        $this->view('create', [
            'entity' => $entity,
            'row' => $row,
            'clients' => $clientRepository->getAllSelectList(),
            'articlesList' => $articleRepository->getAllSelectList(),
            'articles' => $articleRepository->findAllRaw(),
        ]);
    }

    /**
     * @param ArticlesRepository $articleRepository
     * @param ClientsRepository $clientRepository
     * @param int $pk
     */
    public function update(
        ArticlesRepository $articleRepository,
        ClientsRepository $clientRepository,
        $pk
    )
    {
        $this->doUpdate($pk, [
            'row' => $this->rowRepository->create(false),
            'clients' => $clientRepository->getAllSelectList(),
            'articlesList' => $articleRepository->getAllSelectList(),
            'articles' => $articleRepository->findAllRaw(),
        ]);
    }

    /**
     * @param Settings $settings
     * @param int      $pk
     */
    public function printout(Settings $settings, $pk)
    {
        $this->layout = 'printout';
        $item = $this->model
            ->with('rows')
            ->findByPk($pk);

        $this->display('printout', [
            'item'         => $item,
            'contractType' => 'Договор',
            'quantitySum'  => $this->model->getQuantitySum($pk),
            'sender'       => $settings->getRequisites('supplier'),
            'client'       => (object)$settings->getRequisites('firm'),
        ]);
    }
}
