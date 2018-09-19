<?php
namespace app\models;

/**
 * Класс модели строки договора
 * 
 * @author Андрей Сердюк
 * @copyright (c) 2018 IMND
 */
class ContractsRows extends \app\components\RowsModel
{
    use \app\traits\ArticleTrait;

    public static $tableName = 'contracts_rows';
    public static $primKey = 'id';
    public static $fields = array('article_id');

    protected static $parentKey = 'contract_id';
    protected static $fieldTypes = array(
        'article_id' => 'smallint',
    );
    protected static $attributeTypes = array(
        'article_id' => 'select',
    );
    protected static $attributeNames = array(
        'article_id' => 'товар',
    );
    protected $relations = array(
        'article' => array('Articles', 'has_one', 'article_id'),
    );

    public function rules()
    {
        return array_merge(parent::rules(), array(
            'article_id' => array('numerical'),
        ));
    }

    /**
     * getContractRows
     * 
     * @param $contractID integer
     * @return array
     */
    public function getAllByContract($contractID=null)
    {
        return $this->getAllByConditions(array('contract_id' => $contractID));
    }
    
    public function getAllByConditions($where=array())
    {
        return $this
            ->join(
                array('articles' => 'art'),
                array('article_id', 'id')
            )
            ->join(
                array(ArticleSubcats::$tableName => 'subcat'),
                array('subcat_id', 'id'),
                'art'
            )
            ->join(
                array(ArticleCats::$tableName => 'cat'),
                array('cat_id', 'id'),
                'subcat'
            )
            ->select(array(
                '*',
                'art.name' => 'art_name',
                'art.unit' => 'art_unit',
                'subcat.name' => 'subcat_name',
                'cat.name' => 'cat_name',
                'cat.description' => 'cat_description',
            ))
            ->where($where)
            ->sortBy('cat.id')
            ->getAll();
    }
}