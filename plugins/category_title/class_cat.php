<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class CategoryTitle {
	private static $instance;
	private static $catinfo = array();
	
	private function __construct()
	{
		self::init();
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
	
	private static function init()
	{
		$CI = &get_instance();
		$CI->db->select('meta_value');
		$CI->db->join('meta', 'category.category_id = meta.meta_id_obj', 'left');
		$CI->db->where('meta_key', 'category_title');
		$CI->db->where('meta_table', 'category');
		$CI->db->where('category_slug', mso_segment(2));
		$CI->db->from('category');
		$CI->db->limit('1');
		$query = $CI->db->get();
		if ($query->num_rows() > 0)
			$query = $query->row('meta_value');
		else
			$query = 0;
		
		if($query)
		{
			$tmp = explode('|', $query);
			//_pr($tmp);
			self::$catinfo['title'] = trim($tmp[0]);
			self::$catinfo['keywords'] = trim($tmp[1]);
			self::$catinfo['description'] = trim($tmp[2]);
			self::$catinfo['template'] = trim($tmp[3]);
			unset($tmp);
		}
		else
		{
			self::$catinfo['title'] = '';
			self::$catinfo['keywords'] = '';
			self::$catinfo['description'] = '';
			self::$catinfo['template'] = '';
		}
	}
	
	public function __get($method)
	{
		$method = 'get_' . $method;
		if(method_exists($this,$method)){
            return $this->$method();
        }else{
			return false;
		}
	}
	
	private function get_title()
	{
		return self::$catinfo['title'];
	}
	private function get_keywords()
	{
		return self::$catinfo['keywords'];
	}
	private function get_description()
	{
		return self::$catinfo['description'];
	}
	private function get_template()
	{
		return self::$catinfo['template'];
	}
}