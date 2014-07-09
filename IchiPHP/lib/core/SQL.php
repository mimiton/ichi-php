<?php
class SQL {
	
	static function table( $table ) {
		
		$instance = new SQL();
		
		$instance->method    = 'SELECT';
		$instance->table     = $table;
		$instance->conditionLogicNeed = false;
		
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
	 * @desc   获取sql语句
	 * @return string
	 */
	function get() {
		return $this->method. ' ' .$this->fields. ' WHERE ' .$this->condition. ' ORDER BY ' .$this->orderSql;
	}
	
	/**
	 * @desc  添加 SELECT 字段
	 * @param unknown $fields
	 * @throws Exception
	 * @return SQL
	 */
	function select( $fields ) {
		
		if( $this->method !== 'SELECT' ) {
			throw new Exception('SQL WARN: Trying to use SELECT in ' .$this->method. ' operation');
			return $this;
		}
		
		if( is_array($fields) ) {
			
			$str = implode( ',', $fields );
		}
		else if( is_string($fields) )
			$str = $fields;
		
		if( $this->fields )
			$this->fields .= ',';
		
		$this->fields .= $str;
		
		
		return $this;
		
	}
	
	/**
	 * @desc  添加逻辑符，并操作逻辑符开关
	 * @param unknown $logic
	 */
	private function whereLogic( $logic ) {
		
		// 逻辑符开关处于需要状态
		if( $this->conditionLogicNeed ) {
			
			$this->condition .= ' '.$logic.' ';
			
			// 标记逻辑符开关为不需要
			$this->conditionLogicNeed = false;
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
		$this->whereLogic( 'AND' );
		
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
		$this->whereLogic( 'OR' );

		
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
	
}