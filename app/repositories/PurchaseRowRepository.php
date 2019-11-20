<?php
namespace app\repositories;

use
    tachyon\db\dataMapper\Repository,
    app\entities\PurchaseRow;

/**
 * @author Андрей Сердюк
 * @copyright (c) 2018 IMND
 */
class PurchaseRowRepository extends Repository
{
    /**
     * @var app\entities\PurchaseRow
     */
    protected $purchaseRow;

    public function __construct(PurchaseRow $row, ...$params)
    {
        $this->purchaseRow = $row;

        parent::__construct(...$params);
    }
}
