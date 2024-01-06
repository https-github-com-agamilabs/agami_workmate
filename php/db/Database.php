<?php
include_once(dirname(__FILE__) . "/config.php");

class Database
{
  private  $dbcon = null;
  private  $dbName;
  private  $userName;
  private  $password;
  private  $hostName;

  public function db_connect()
  {
    $this->dbName = constant('PREFIX') . constant('DB_NAME');
    $this->userName = constant('PREFIX') .constant('DB_USER');
    $this->password = constant('DB_PASSWORD');
    $this->hostName = constant('DB_HOST');

    $this->dbcon = new mysqli("$this->hostName", "$this->userName", "$this->password", "$this->dbName");
    if ($this->dbcon->connect_errno) {
      throw new Exception("Database connection could not be established: " . $this->dbcon->connect_error);
    }
    $this->dbcon->set_charset("utf8");
    return $this->dbcon;
  }

  public function is_connected()
  {
    if ($this->dbcon != null)
      return true;
    else return false;
  }

  public function get_table_name($name)
  {
    $pre = constant('PREFIX');
    $pre = $pre . "_";
    $tableName = $pre . $name;
    return $tableName;
  }
}
