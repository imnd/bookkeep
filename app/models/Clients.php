<?php
namespace app\models;

/**
 * Класс модели клиентов фирмы
 * 
 * @author Андрей Сердюк
 * @copyright (c) 2018 IMND
 */
class Clients extends \tachyon\db\models\ArModel
{
    use \tachyon\dic\behaviours\Active;
    use \tachyon\dic\behaviours\ListBehaviour;
    use \tachyon\traits\GetList;

    public static $primKey = 'id';
    public static $tableName = 'clients';
    public static $fields = array('name', 'address', 'region_id', 'telephone', 'fax', 'contact_fio', 'contact_post', 'account', 'bank', 'INN', 'KPP', 'BIK', 'sort', 'active');

    protected static $fieldTypes = array(
        'id' => 'smallint',
        'region_id' => 'smallint',
        'name' => 'tinytext',
        'address' => 'tinytext',
        'telephone' => 'tinytext',
        'fax' => 'tinytext',
        'contact_fio' => 'tinytext',
        'contact_post' => 'tinytext',
        'account' => 'tinytext',
        'bank' => 'tinytext',
        'INN' => 'bigint',
        'KPP' => 'bigint',
        'BIK' => 'bigint',
        'sort' => 'smallint',
        'active' => 'enum',
    );
    protected static $attributeTypes = array(
        'name' => 'input',
        'address' => 'input',
        'region_id' => 'select',
        'telephone' => 'input',
        'fax' => 'input',
        'contact_fio' => 'input',
        'contact_post' => 'input',
        'account' => 'input',
        'bank' => 'input',
        'INN' => 'input',
        'KPP' => 'input',
        'BIK' => 'input',
        'sort' => 'input',
        'active' => 'checkbox',
    );
    protected static $attributeNames = array(
        'name' => 'название',
        'address' => 'адрес',
        'region_id' => 'район',
        'telephone' => 'телефон',
        'fax' => 'факс',
        'contact_fio' => 'контакт. лицо',
        'contact_post' => 'должность конт. лица',
        'account' => 'расчетный счет',
        'bank' => 'в банке',
        'INN' => 'ИНН',
        'KPP' => 'КПП',
        'BIK' => 'БИК',
        'sort' => 'порядок сортировки',
        'active' => 'активный',
    );
    protected $defSortBy = array('sort');
    protected $entityNames = array(
        'single' => 'клиент',
        'plural' => 'клиенты'
    );
    protected $relations = array(
        'region' => array('Regions', 'belongs_to', 'region_id'),
    );

    public function rules()
    {
        return array(
            'name' => array('alphaExt', 'required'),
            'address' => array('alphaExt'),
        );
    }

    public function setSearchConditions($where=array())
    {
        $this->like($where, 'name');
        $this->like($where, 'address');

        return $this;
    }
}