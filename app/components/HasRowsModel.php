<?php
namespace app\components;

/**
 * @author Андрей Сердюк
 * @copyright (c) 2018 IMND
 * 
 * class HasRowsModel
 * Класс модели табличного документа
 */
class HasRowsModel extends \tachyon\db\models\ArModel
{
    protected $rowModelName;
    protected $rowFk;

    public function __construct()
    {
        $this->relations['rows'] = array($this->rowModelName, 'has_many', $this->rowFk);

        parent::__construct();
    }

    public function getItem($where=array())
    {
        $item = $this
            ->addWhere($where)
            ->getOne();
            
        $item['rows'] = $this->get($this->rowModelName)
            ->addWhere(array(
                $this->rowFk => $item['id'],
            ))
            ->getAll();

        return $item;
    }

    public function getTotal($where=array())
    {
        if ($result = $this
            ->addWhere($where)
            ->select('SUM(sum) AS total')
            ->getAll())
            return $result[0]['total'];
        
        return 0;
    }

    public function getQuantitySum($pk)
    {
        if ($result = $this
            ->addWhere(array(static::$primKey => $pk))
            ->joinRelation(array('rows' => 'r'))
            ->select('SUM(r.quantity) AS quantitySum')
            ->getAll())
            return $result[0]['quantitySum'];
        
        return 0;
    }

    protected function afterSave()
    {
        // удаляем строки
        $this->delRelModels('rows');

        $sum = 0;
        if (isset($_POST[$this->rowModelName])) {
            $rowsData = \tachyon\helpers\ArrayHelper::transposeArray($_POST[$this->rowModelName]);
            $thisPk = $this->getPrimKeyVal();
            $rowFk = $this->rowFk;
            foreach ($rowsData as $rowData) {
                $row = $this->get($this->rowModelName);
                $row->setAttributes($rowData);
                $row->$rowFk = $thisPk;
                $row->save();
                $sum += $row->sum;
            }
        }
        $this->saveAttrs(compact('sum'));
    }
    
    # геттеры
    
    public function getRowModelName()
    {
        return $this->rowModelName;
    }
}