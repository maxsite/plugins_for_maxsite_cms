<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
/*

# версия free

//возвращает свойства для массива категорий
CategoryEditor::getInstance()->categoryes_values(array());

// id родителя категории
$parent_id = CategoryEditor::getInstance()->_parent;

// название категории
$name = CategoryEditor::getInstance()->_name;

// описание категории
$descr = CategoryEditor::getInstance()->_desc;

// slug категории
$cat_slug = CategoryEditor::getInstance()->_slug;

// порялок категории
$cat_order = CategoryEditor::getInstance()->_menu_order;

// шаблон вывода категории
$cat_tpl = CategoryEditor::getInstance()->template;

// title категории
$cat_tpl = CategoryEditor::getInstance()->title;

// keywords категории
$cat_tpl = CategoryEditor::getInstance()->keywords;

// description категории
$cat_description = CategoryEditor::getInstance()->description;


// заргузка свойств другой категории по ее ID ($category_id)
CategoryEditor::getInstance()->load = $category_id;

// заргузка свойств другой категории по ее slug ($category_slug)
CategoryEditor::getInstance()->load_from_slug = $category_slug;

// получение свойств массива ID категорий $cats_id = array(1, 8, 10 ...)
$res = CategoryEditor::getInstance()->categoryes_values($cats_id);
pr($res);

// получить массив свойств текущей категории
$res = CategoryEditor::getInstance()->all_params();



// получить префикс
$res = CategoryEditor::getInstance()->prefix;

#
	вывод своих значений
#
значение ключа my_key
$res = CategoryEditor::getInstance()->my_key;


*/
 
class CategoryEditor
{
	private static $instance, $all_meta;
	private static $prefix = '_ce_';
	private static $category_id;
	
	
	private function __construct()
	{
		self::$all_meta = array();
	}
	
	public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
	
	private function __clone(){}
	private function __sleep(){}
	private function __wakeup(){}
	
	public function __get($method)
	{
		$method = $method;
		return $this->$method();
	}
	
	public function __set($method, $values)
	{
		//return $method;
		$method = 'set_' . $method;
		$this->$method($values);
	}
	
	public function __call($method, $values)
	{
		
		$key = $method;
		
		
		if(isset(self::$all_meta[$key]))
		{
			return self::$all_meta[$key];
		}
		else
		{
			return '';
		}
	}
	
	private function prefix()
	{
		return self::$prefix;
	}
	
	private function category_id()
	{
		return self::$category_id;
	}
	
	private function set_load($cat_id = 0)
	{
		if(!$cat_id) return false;
		self::$category_id = $cat_id;
		
		$buffer = $this->_get_property($cat_id);
		if(isset($buffer[$cat_id]))
			self::$all_meta = $buffer[$cat_id];
		
		unset($buffer);
	}
	
	
	//загрузка свойств по slug категории
	private function set_load_from_slug($slug = '')
	{
		$cat_id = ce_get_slug_id_category($slug, 'id');
		$this->set_load($cat_id);
	}
	
	//получение свойств из БД
	private function _get_property($cat_id)
	{
		$CI = & get_instance();
		$buffer = array();
		# характеристики из таблицы category
		$buffer = $this->_get_prop_cat($cat_id);
		
		if(!$buffer) return array();
		
		
		
		$CI->db->select('meta_id_obj, meta_key, meta_value');
		$CI->db->from('meta');
		$CI->db->where('meta_table', 'category');
		if(is_array($cat_id))
		{
			$CI->db->where_in('meta_id_obj', $cat_id);
			$arr = true;
		}
		else
		{
			$CI->db->where('meta_id_obj', $cat_id);
			$arr = false;
		}		
		$CI->db->like('meta_key', self::$prefix, 'after');
		$query = $CI->db->get();
		
		foreach($query->result_array() as $row)
		{
			$meta_key = str_replace(self::$prefix, '', $row['meta_key']);
			$buffer[$row['meta_id_obj']][$meta_key] = $row['meta_value'];
		}
		
		return $buffer;
	}
	//получение свойств для массива прочих категорий
	public function categoryes_values($cats = array())
	{
		if(!$cats or !is_array($cats)) return array();
		$out = $this->_get_property($cats);		
		unset($val, $arr);
		return $out;
	}
	
	//выборка данных из таблицы категорий
	private function _get_prop_cat($cat_id)
	{
		$buffer = array();
		$CI = & get_instance();
		$CI->db->select('category_id, category_id_parent, category_type, category_name, category_desc, category_slug, category_menu_order');
		$CI->db->from('category');
		if(is_array($cat_id))
		{
			$CI->db->where_in('category_id', $cat_id);
		}
		else
		{
			$CI->db->where('category_id', $cat_id);
		}
		$query = $CI->db->get();
		foreach($query->result_array() as $row)
		{
			$buffer[$row['category_id']]['_parent'] = $row['category_id_parent'];
			$buffer[$row['category_id']]['_type'] = $row['category_type'];
			$buffer[$row['category_id']]['_name'] = $row['category_name'];
			$buffer[$row['category_id']]['_desc'] = $row['category_desc'];
			$buffer[$row['category_id']]['_slug'] = $row['category_slug'];
			$buffer[$row['category_id']]['_menu_order'] = $row['category_menu_order'];
		}
		return $buffer;
	}
	
	// загруженный параметры категории
	public function all_params()
	{
		return self::$all_meta;
	}
	
	
}

