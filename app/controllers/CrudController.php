<?php
namespace app\controllers;

use tachyon\Controller,
    tachyon\components\Flash,
    tachyon\db\dataMapper\Entity,
    tachyon\dic\Container;

/**
 * class Controller
 * Базовый класс для всех контроллеров
 * 
 * @author Андрей Сердюк
 * @copyright (c) 2018 IMND
 */
class CrudController extends Controller
{
    use \tachyon\traits\Authentication;

    protected $layout = 'crud';
    /**
     * @var \app\interfaces\RepositoryInterface
     */
    protected $repository;

    protected $postActions = array('delete');
    /** @inheritdoc */
    protected $protectedActions = '*';

    /**
     * @var tachyon\components\Flash
     */
    protected $flash;

    public function __construct(Flash $flash, ...$params)
    {
        $this->flash = $flash;

        parent::__construct(...$params);
    }

    /**
     * Хук, срабатывающий перед запуском экшна
     * @return boolean
     */
    public function beforeAction()
    {
        if ($this->protectedActions==='*' || in_array($this->action, $this->protectedActions)) {
            $this->checkAccess();
        }
        return true;
    }

    /**
     * Главная страница, список сущностей раздела
     * 
     * @param Entity $entity
     * @param array $params
     */
    protected function _index(Entity $entity, $params = array())
    {
        $this->view('index', array_merge([
            'entity' => $entity,
            'items' => $this
                ->repository
                ->setSearchConditions($this->get)
                ->setSort($this->get)
                ->findAll(),
        ], $params));
    }

    /**
     * @param int $pk
     * @param array $params
     */
    protected function _update($pk, $params)
    {
        /**
         * @var Entity $entity
         */
        $entity = $this->getEntity($pk);
        if ($this->save($entity)) {
            $this->redirect("/{$this->id}");
        }
        $this->view('update', array_merge(compact('entity'), $params));
    }

    /**
     * 
     */
    protected function _create($params)
    {
        /**
         * @var Entity $entity
         */
        $entity = $this->repository->create();
        if ($this->save($entity)) {
            $this->redirect("/{$this->id}");
        }
        $this->view('create', array_merge(compact('entity'), $params));
    }

    /**
     * @param Entity $entity
     * @return void
     */
    protected function save(Entity $entity)
    {
        if (!empty($this->post)) {
            $entity->setAttributes($this->post);
            if ($entity->save()) {
                $this->flash->setFlash('Сохранено успешно', Flash::FLASH_TYPE_SUCCESS);
                return true;
            }
            $this->flash->setFlash("Что то пошло не так, {$entity->getErrorsSummary()}", Flash::FLASH_TYPE_ERROR);
            return false;
        }
    }

    /**
     * @param int $pk
     */
    public function delete($pk)
    {
        echo json_encode([
            'success' => $this->getEntity($pk)->delete()
        ]);
    }

    /**
     * @param int $pk
     * @return Entity
     */
    protected function getEntity($pk)
    {
        if (!$entity = $this->repository->findByPk($pk)) {
            $this->error(404, $this->msg->i18n('Wrong address.'));
        }
        return $entity;
    }
}