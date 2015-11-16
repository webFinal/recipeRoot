<?php
class DataConnection extends mysqli {
	private static $dataConnection = null;
	
	private function __construct($host, $user, $pass, $db) {
        parent::__construct($host, $user, $pass, $db);

        if (mysqli_connect_error()) {
            die(mysqli_connect_error());
        }
    }

	public static function getInstance() {
		if (is_null(self::$dataConnection)) 
			self::$dataConnection = new DataConnection('52.32.112.5', 'recipe_user', 'recipe_user2015', 'recipe_db');
		return self::$dataConnection;
	}
}

class Data {
	private $sql;
	private $rs;
	
	public $table;
	public $key;
	public $columns;
	
	public function init($items) {
		$this->table = $items['table'];
		$this->columns = $items['columns'];
		$this->key = $items['key'];
		$this->reset();
	}
	
	public function reset() {
		foreach ($this->columns as $objCol => $dbCol) {
			$this->$objCol = null;
		}
	}
	
	private function fetch() {
		//return mysql_fetch_assoc($this->rs);
		return $this->rs->fetch_assoc();
	}
	
	private function query() {
		$connection = DataConnection::getInstance();
		//echo $this->sql . "<hr>";
		//$this->rs = mysql_query($this->sql, $connection) or die(mysql_error());
		$this->rs = $connection->query($this->sql);
		return $connection->affected_rows;
	}
	
	public function load($v = null) {
		$key = $this->key;
		if (!$v) {
			$v = $this->$key;                                                                        //$this->$key;
		}
		$this->sql = "SELECT * FROM {$this->table} WHERE {$this->columns[$key]} = {$v}";
		if ($this->query()) {
			$asResult = $this->fetch();
			foreach ($this->columns as $objCol => $dbCol) {
				$this->$objCol = $asResult[$dbCol];
			}
			return $this;
		} 
		return false;
	}
	
	public function find() {
		$key = $this->key;
		$where = " WHERE 1 = 1 ";
		$limit = " LIMIT 0, 10";
		foreach ($this->columns as $objCol => $dbCol) {
			if (!is_null($this->$objCol)) {
				$where .= " AND {$dbCol} = '{$this->$objCol}'";
			}
		}

		$this->sql = "SELECT * FROM {$this->table} {$where} {$limit}";
		$asResult = array();
		if ($this->query()) {
			while($a = $this->fetch()) {
				$o = clone $this;
				foreach ($o->columns as $objCol => $dbCol) {
					$o->$objCol = $a[$dbCol];
				}
				$asResult[] = $o;
			}
		}
		return $asResult;
	}
	
}

class Category extends Data {
    function __construct() {
		$items = array(
			'table' => 'category',
			'key' => 'id',
			'columns' => array(
				'id' => 'cid',
				'parentId' => 'pid',
				'name' => 'cname'
							   ));
		parent::init($items);
	}
	
	public function name() {
		$this->parentId = 1;
		return $this->find();
	}
	
	public function rep() {
		$r = new Recipe();
		$r->categoryId = $this->id;
		return $r->find();
	}
}

class User extends Data {
    function __construct() {
		$items = array(
					   'table' => 'user',
					   'key' => 'id',
					   'columns' => array(
										  'id' => 'uid',
										  'emailAddress' => 'email',
										  'userName' => 'uname')
					   );
		parent::init($items);
	}
	
	public function rep() {
		$r = new Recipe();
		$r->userId = $this->id;
		return $r->find();
	}
}


class Recipe extends Data {
    function __construct() {
		$items = array(
					   'table' => 'recipe',
					   'key' => 'id',
					   'columns' => array(
										  'id' => 'rid',
										  'name' => 'rname',
										  'reIngredient' => 'ingredient',
										  'reInstruction' => 'instruction',
										  'mainPic' => 'main_pic',
										  'creatTime' => 'create_time',
										  'categoryId' => 'cid',
										  'userId' => 'uid'));
		parent::init($items);
	}
	
	function load($v = null) {
		parent::load($v);
		
		$this->Category = new Category();
		$this->Category->id = $this->categoryId;
		
		$this->User = new User();
		$this->User->id = $this->userId;
		
		return $this;
	}
	
	function newest() {
		return $this->find();
	}
	
}

?>