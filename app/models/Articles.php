<?php
namespace app\models;

/**
 * Класс модели товаров
 * 
 * @author Андрей Сердюк
 * @copyright (c) 2018 IMND
 */
class Articles extends \tachyon\db\models\ArModel
{
    use \tachyon\dic\behaviours\Active;
    use \tachyon\dic\behaviours\ListBehaviour;
    use \tachyon\traits\GetList;

    public static $primKey = 'id';
    public static $tableName = 'articles';
    public static $fields = array('subcat_id', 'name', 'unit', 'price', 'active');

    protected static $fieldTypes = array(
        'id' => 'smallint',
        'subcat_id' => 'smallint',
        'name' => 'tinytext',
        'unit' => 'float',
        'price' => 'tinyint',
        'active' => 'enum',
    );
    protected static $attributeNames = array(
        'subcat_id' => 'подкатегория',
        'subcatName' => 'подкатегория',
        'name' => 'название',
        'unit' => 'ед.изм.',
        'price' => 'цена',
        'priceFrom' => 'цена от',
        'priceTo' => 'цена до',
        'active' => 'активный',
        'activeText' => 'активный',
    );
    protected $defSortBy = array('name');
    protected $entityNames = array(
        'single' => 'товар',
        'plural' => 'товары'
    );
    protected $relations = array(
        'subcategory' => array('ArticleSubcats', 'belongs_to', 'subcat_id'),
    );

    public function setSearchConditions($where=array())
    {
        $this->gt($where, 'price', 'priceFrom');
        $this->lt($where, 'price', 'priceTo');
        $this->addWhere($where);

        return $this;
    }

    /**
     * @return string
     */
    public function getCatDescription()
    {
        return $this->subcategory->category->description;
    }

    /**
     * @return string
     */
    public function getSubcatName()
    {
        return $this->subcategory->name;
    }

    /**
     * @return array
     */
    public function getUnits()
    {
        return array('кг', 'шт');
    }
}