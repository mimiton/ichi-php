<?php
class SQL {
	
	static function table( $table ) {
		
		$instance = new SQL();
		
		$instance->method    = 'SELECT';
		$instance->tableName = $table;
		$instance->conditionLogicNeed = false;
		$instance->joinLogicNeed = false;
		
		return $instance;
		
	}
	
	/**
	 * @desc  转换json成where条件
	 * @param unknown $json
	 * @return string
	 */
	static function json2ConditionString( $json ) {
		$arr = json_decode( stripcslashes($json), true );
		return self::arr2ConditionString( $arr );
	}
	
	/**
	 * @desc  转换数组成where条件
	 * @param unknown $arr
	 * @return string
	 */
	static function arr2ConditionString( $arr ) {
		if( !is_array($arr) )
			return '';
		
		if( !is_array($arr[0]) )
			$arr = array($arr);
		
		$str = '';
		foreach ( $arr as $i => $v ) {
			
			// 逻辑符
			$str .= ' ' .strtoupper( $v[0] ). ' ';
			
			// 包含子条件，递归
			if( is_array($v[1]) )
				$str .= '( ' . self::arr2ConditionString( $v[1] ) . ' )';
			
			// 正常添加
			else {

				// 大写操作符
				$operator = strtoupper($v[2]);
				
				// 操作符为(NOT )IN的情况
				if( $operator == 'IN' || $operator == 'NOT IN' ) {

					if( is_array($v[3]) ) 
						$value_1 = implode( ',', self::wrapValue($v[3]) );
					
					else
						$value_1 = $v[3];
					
					$value_1 = '(' .$value_1. ')';
					
				}
				// 普通操作符情况
				else
					$value_1 = self::wrapValue($v[3]);
				
				// 添加语句
				$str .= '`' .$v[1]. '` ' .$operator. ' ' .$value_1;
				
				// 操作符为(NOT )BETWEEN，补充添加第二个值
				if( $operator == 'BETWEEN' || $operator == 'NOT BETWEEN' ) {
					$str .= ' AND ' .self::wrapValue($v[4]);
				}
				
				$str .= ' ';
			}
		}
		return $str;
	}
	
	/**
	 * @desc  用引号包裹非数字的值
	 * @param unknown $str
	 * @return unknown|string
	 */
	static function wrapValue( $strOrArr ) {
		if( is_string($strOrArr) )
			if( is_numeric($strOrArr) )
				return $strOrArr;
			else
				return '"' .$strOrArr. '"';
		else if( is_array($strOrArr) ) {
			
			foreach ( $strOrArr as $k => $v )
				$strOrArr[$k] = self::wrapValue($v);
			
			return $strOrArr;
		}
	}
	
	/**
	 * @desc  转换字段列表数组为字符串
	 * @param unknown $fields
	 * @return string|unknown
	 */
	static function fields2string( $fields ) {
		
		if( is_array($fields) )
			return implode( ',', $fields );
		else if( is_string($fields) )
			return $fields;
		
	}
	
	
	/**
	 * @desc   获取sql语句
	 * @return string
	 */
	function get() {
		
		if( $this->method == 'SELECT' )
			$sql = $this->getSelect();
		else if( $this->method == 'UPDATE' )
			$sql = $this->getUpdate();
		else if( $this->method == 'INSERT' )
			$sql = $this->getInsert();

		if( $this->condition )
			$sql .= ' WHERE '.$this->condition;
		if( $this->orderSql )
			$sql .= ' ORDER BY '.$this->orderSql;
		if( $this->groupSql )
			$sql .= ' GROUP BY '.$this->groupSql;
		if( $this->limitSql )
			$sql .= ' LIMIT '.$this->limitSql;
	
		if( $this->joinSql )
			$sql .= ' '.$this->joinSql;
		
		return $sql;
	}
	
	/**
	 * @desc   获取SELECT查询语句
	 * @return string
	 */
	private function getSelect() {
		
		$sql =	'SELECT '.$this->fields.' FROM `'.$this->tableName.'`';
		
		return $sql;
		
	}
	
	/**
	 * @desc   获取UPDATE查询语句
	 * @return string
	 */
	private function getUpdate() {
		
		$sql = 'UPDATE `'.$this->tableName.'` SET ';
		
		$valsArr = array();
		foreach ( $this->valuesToUpdate as $k => $v ) {
			
			// 处理自增/减/乘/除
			if( preg_match( '/^([\+\-\*\/])=(\d+)$/i', $v, $matches ) )
				$valsArr[] = '`'.$k.'`=`'.$k.'`'.$matches[1].$matches[2];
			else
				$valsArr[] = '`'.$k.'`='.SQL::wrapValue($v);
			
		}
		$sql .= implode( ',', $valsArr );
		
		return $sql;
		
	}
	
	/**
	 * @desc   获取INSERT查询语句
	 */
	private function getInsert() {
		$sql = 'INSERT INTO `'.$this->tableName.'` ';
		
		foreach ( $this->valuesToInsert as $k => $v ) {
			$fields .= '`'.$k.'`,';
			$values .= SQL::wrapValue($v).',';
		}
		$fields = substr($fields,0,-1);
		$values = substr($values,0,-1);
		
		$sql .= '('.$fields.') VALUES ('.$values.')';
		
		return $sql;
	}
	
	/**
	 * @desc  添加 SELECT 字段
	 * @param unknown $fields
	 * @throws Exception
	 * @return SQL
	 */
	function select( $fields ) {
		
		if( $this->method !== 'SELECT' )
			return $this;
		
		// 转换字段列表为逗号连接的字符串
		$str = SQL::fields2string( $fields );
		
		$this->fields .= ($this->fields? ',' : '') . $str;
		
		return $this;
		
	}
	
	/**
	 * @desc  设置sql的操作为UPDATE，并添加字段
	 * @param unknown $fields
	 * @return SQL
	 */
	function update( $field, $value = NULL ) {
		
		$this->method = 'UPDATE';
		
		if( is_string($field) )
			$field = array( $field => $value );
		
		if( is_array($field) )
			foreach ( $field as $k => $v )
				$this->valuesToUpdate[ $k ] = $v;
		
		return $this;
		
	}
	
	/**
	 * @desc 设置操作为INSERT
	 */
	function insert( $field = NULL, $value = NULL ) {
		$this->method = 'INSERT';
		
		if( $field )
			$this->addValue( $field, $value );
		
		return $this;
	}
	
	/**
	 * @desc  添加待插入的键值对
	 * @param unknown $field
	 * @param unknown $value
	 * @return SQL
	 */
	function addValue( $field, $value = NULL ) {
		
		if( is_string($field) )
			$field = array( $field => $value );
		
		if( is_array($field) )
			foreach ( $field as $k => $v )
				$this->valuesToInsert[ $k ] = $v;		
		
		return $this;
		
	}
	
	/**
	 * @desc  添加逻辑符，并操作逻辑符开关
	 * @param unknown $logic
	 */
	private function addLogic( $logic, $mode = 'WHERE' ) {
		
		// 条件逻辑符开关处于需要状态
		if( $mode == 'WHERE' && $this->conditionLogicNeed ) {
			
			$this->condition .= ' '.$logic.' ';
			
			// 标记逻辑符开关为不需要
			$this->conditionLogicNeed = false;
		}
		else if( $mode == 'JOIN' && $this->joinLogicNeed ) {
			$this->joinSql .= ' '.$logic.' ';
			$this->joinLogicNeed = false;
		}
	}
	
	/**
	 * @desc  添加 AND 的where条件
	 * @param unknown $field
	 * @param unknown $operator
	 * @param unknown $value
	 * @return SQL
	 */
	function where( $field, $operator, $value, $value_2 = NULL ) {
		
		// 添加AND逻辑符
		$this->addLogic( 'AND' );
		
		// 添加条件
		$this->condition .= SQL::arr2ConditionString(array( '', $field, $operator, $value, $value_2 ));

		// 开启逻辑符开关
		$this->conditionLogicNeed = true;
		
		return $this;
		 
	}
	
	/**
	 * @desc  添加 OR 的where条件
	 * @param unknown $field
	 * @param string $operator
	 * @param string $value
	 * @return SQL
	 */
	function orWhere( $field, $operator = NULL, $value = NULL, $value_2 = NULL ) {
		
		// 添加OR逻辑符
		$this->addLogic( 'OR' );

		
		// 数组形式子条件
		if( is_array($field) ) {
			$this->condition .= '(' . SQL::arr2ConditionString($field) . ')';
		}
		else if( !$operator || !$value ) {
			
			// json形式子条件
			if( is_string($field) )
				$this->condition .= '(' . SQL::json2ConditionString($field) . ')';
			// 匿名方法子条件
			else {
				$this->condition .= '(';
				$field($this);
				$this->condition .= ')';
			}
		}
		// 普通形式
		else {
			$this->condition .= SQL::arr2ConditionString( array( '', $field, $operator, $value, $value_2 ) );
		}
		
		// 开启逻辑符开关
		$this->conditionLogicNeed = true;
		
		return $this;
		
	}
	
	/**
	 * @desc  排序
	 * @param unknown $field
	 * @param string $order
	 * @return SQL
	 */
	function orderBy( $field, $order = 'ASC' ) {
		
		$order = strtoupper($order);
		
		if( !empty($this->orderSql) )
			$this->orderSql .= ',';
		
		$this->orderSql .= '`'.$field.'` '.$order;
		
		return $this;
	}
	
	/**
	 * @desc  设置 GROUP BY 语句
	 * @param unknown $field
	 * @return SQL
	 */
	function groupBy( $field ) {
		$this->groupSql = $field;
		return $this;
	}
	
	/**
	 * @desc 设置LIMIT参数
	 * @param unknown $s
	 * @param unknown $n
	 * @return SQL
	 */
	function limit( $s, $n = NULL ) {
		
		$this->limitSql = $s;
		
		if( !empty($n) )
			$this->limitSql .= ',' . $n;
		
		return $this;
		
	}
	
	/**
	 * @desc  添加join语句
	 * @param unknown $table
	 * @param unknown $field_1
	 * @param string $operator
	 * @param string $field_2
	 * @param string $mode
	 * @return SQL
	 */
	function join( $table, $field_1, $operator = NULL, $field_2 = NULL, $mode = '' ) {
		
		$this->joinSql .= $mode .' JOIN `'. $table .'` ON ';
		
		if( isset($field_1) && isset($operator) && isset($field_2) ) {
			$this->on( $field_1, $operator, $field_2 );
		}
		// 其余参数为空，$field_1作function使用，在其内部添加多个on连接字段
		else
			$field_1( $this );
		
		return $this;
	}
	
	/**
	 * @desc  添加join的on连接字段
	 * @param unknown $field_1
	 * @param unknown $operator
	 * @param unknown $field_2
	 * @param string $logic
	 * @return SQL
	 */
	function on( $field_1, $operator, $field_2, $logic = 'AND' ) {
		$this->addLogic( $logic, 'JOIN' );
		$this->joinSql .= $field_1 .' '. $operator .' '. $field_2 .' ';
		$this->joinLogicNeed = true;
		return $this;
	}
	
	/**
	 * @desc  `或` 逻辑的join连接语句
	 * @param unknown $field_1
	 * @param unknown $operator
	 * @param unknown $field_2
	 * @return SQL
	 */
	function orOn( $field_1, $operator, $field_2 ) {
		$this->on( $field_1, $operator, $field_2, 'OR' );
		return $this;
	}
	
}