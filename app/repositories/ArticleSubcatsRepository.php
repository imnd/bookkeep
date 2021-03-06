<?php
namespace app\repositories;

use tachyon\db\dataMapper\Repository,
    tachyon\traits\RepositoryListTrait,
    app\entities\ArticleSubcat;

/**
 * @author Андрей Сердюк
 * @copyright (c) 2020 IMND
 */
class ArticleSubcatsRepository extends Repository
{
    use RepositoryListTrait;

    /**
     * @param ArticleSubcat $entity
     * @param array $params
     */
    public function __construct(ArticleSubcat $entity, ...$params)
    {
        $this->entity = $entity;

        parent::__construct(...$params);
    }
}
