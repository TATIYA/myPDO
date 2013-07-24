<?php
/*
 * Copyright 2013 LHOUCINE TATIYA <contact@tatya.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 *
 * 19/04/2013 19:23:05 
 */
$mypdo_err = array(
1 => '<h1>php database object not found!</h1>',
2 => 'You must define the hostname, database name, username and password',
3 => '<h1>Oops! Can\'t connect to the database</h1>',
4 => 'You must make a query to return the nember of rows',
);
// Check is PDO class exists
if(!class_exists('PDO')){
  die($mypdo_err[1]);
}
class myPDO{
	private $db;
	private $query;
	private $show_errors = true;

	/*
	 *
	 * name: function show_errors
	 * @param: no param,
	 * @return no return
	 * @access public
	 *
	 */
	public function show_errors()
	{
		// Turn error handling On
		$this->show_errors = true;
	}

	/*
	 *
	 * name: function hide_errors
	 * @param: no param,
	 * @return no return
	 * @access public
	 *
	 */
	public function hide_errors()
	{
		// Turn error handling Off
		$this->show_errors = false;
	}

	/*
	 *
	 * name: function __construct
	 * @param: string $dbHost (hostname mysql database server)
	 * @param: string $dbName (mysql database name)
	 * @param: string $dbUser (mysql database username)
	 * @param: string $dbPass (mysql database password)
	 * @return: no return
	 * @access public
	 *
	 */
	public function __construct($dbHost=null,$dbName=null,$dbUser=null,$dbPass=null){
		global $mypdo_err;
		// Must have a hostname,database name and username
		if ( ! $dbHost || ! $dbName || ! $dbUser )
		{
			($this->show_errors) ? trigger_error($mypdo_err[2],E_USER_WARNING) : null;
		}
		// Try to connect to database
		try
		{
			$this->db = @new PDO('mysql:host='.$dbHost.';dbname='.$dbName.';charset=utf8', $dbUser, $dbPass);
		}
		catch(Exception $e)
		{
			// Print error and stop execution of script
			echo $mypdo_err[3];
			($this->show_errors) ? trigger_error($e->getMessage(),E_USER_WARNING) : null;
			exit();
		}
	}

	/*
	 *
	 * name: function query
	 * @param: string $sql
	 * @param: array $param (list of parametre as an array)
	 * @return bool
	 * @access public
	 *
	 */
	public function query($sql ,$param=null){
		global $mypdo_err;
		$return_values = false;
		// Prepare SQL query
		$this->query = $this->db->prepare($sql);
		// Check the execution
		if(!$this->query->execute($param)){
			($this->show_errors) ? trigger_error($this->query->errorInfo()[2],E_USER_WARNING) : null;
		}
		else{
			$return_values = true;
		}
		return $return_values;
	}

	/*
	 *
	 * name: function debug
	 * @param: no param
	 * @return no return
	 * @access public
	 *
	 */
	public function debug(){
		// Debug prepared SQL query
		echo $this->query->debugDumpParams();
	}

	/*
	 *
	 * name: function fetch
	 * @param: string $sql
	 * @param: string $type (ARR or OBJ),
	 * @return array or object
	 * @access public
	 *
	 */
	public function fetch($type=null){
		$return_values = false;
		if($type == 'OBJ'){
			// Return data as object
			$return_values = $this->query->fetch(PDO::FETCH_OBJ);
		}elseif($type == 'ASSOC'){
			// Return data as assoc array
			$return_values = $this->query->fetch(PDO::FETCH_ASSOC);
		}elseif($type == 'NUM'){
			// Return data as numeric indexed array
			$return_values = $this->query->fetch(PDO::FETCH_NUM);
		}else{
			// Return data as numeric and associative indexed array(default)
			$return_values = $this->query->fetch();
		}
		return $return_values;
		$this->query->closeCursor();
	}

	/*
	 *
	 * name: function num_rows
	 * @param: no
	 * @return intiger
	 * @access public
	 *
	 */
	public function num_rows(){
		global $mypdo_err;
		$return_values = false;
		if(method_exists($this->query,'rowCount'))
			$return_values = $this->query->rowCount();
		else
			($this->show_errors) ? trigger_error($mypdo_err[4],E_USER_WARNING) : null;
		return $return_values;
	}

	/*
	 *
	 * name: function get_val
	 * @param: intiger $x (col index nember start from o)
	 * @return intiger
	 * @access public
	 *
	 */
	public function get_val($x=0){
		$results = $this->fetch();
		// Return first result
		return $results[$x];
	}

	/*
	 *
	 * name: function version
	 * @param: no param
	 * @return float
	 * @access public
	 *
	 */
	public function version(){
		// Return mysql server version
		return $this->db->getAttribute(PDO::ATTR_SERVER_VERSION);
	}

	/*
	 *
	 * name: function close
	 * @param: no param
	 * @return no return
	 * @access public
	 *
	 */
	public function close(){
		// Close connexion
		$this->db = null;
	}
}
$db = new myPDO($DBhost,$DBname,$DBuser,$DBpass);
?>
