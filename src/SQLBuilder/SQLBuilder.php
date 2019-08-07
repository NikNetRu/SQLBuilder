<?php
namespace SQLBuilder;
use PDO;
/*
 * Фраза запроса комманда - имя таблицы/или где выполнять запрос - условие - постусловие
 */
require_once 'Insert.php';
require_once 'Select.php';
require_once 'Delete.php';
require_once 'Engine.php';
require_once 'Update.php';

class SQLBuilder 
{
    
        private $host = null;
        private $login = null;
        private $password = null;
        private $database = null;
        private $result = null;
        private $query = null;  // результирующий запрос
        private $shemeCall = null; //содержит обьект запроса
        //
    // при обьявлении экземпляра можно указать свойства подключения к БД
    function __construct ($host = null, $login = null, $password = null, $database = null) 
    {
        $this->host = $host;
        $this->login = $login;
        $this->password = $password;
        $this->database = $database;
    }
    
    function setQuerySettings ($host, $database, $login, $password)
    {
        $this->host = $host;
        $this->login = $login;
        $this->password = $password;
        $this->database = $database;
    }
    
    /*
     * команда INSERT SQL - создаёт класс внутри обекта SQL Builder
     * $tablename - имя таблицы 
     */
    function insert ($tablename)
    {
        $this->shemeCall = new Insert($tablename);
    }
    
    function select ($tablename) 
    {
        $this->shemeCall = new Select($tablename);
    }
    
    function delete ($tablename) 
    {
        $this->shemeCall = new Delete($tablename);
    }
    
    function update ($tablename) 
    {
        $this->shemeCall = new Update($tablename);
    }
    
    
    /*
     * Condition - условие которое требуется передать
     * Для всех запросов $condition должен иметь форму: ['name=Andrey', 'age=>28',...]
     * может быть как 1 так и несколько условий.
     * Внутри функции реализуются все необходимые преобразования для запроса
     * Для запросов в которых непредусмотрено условий выведется сообщение об ошибки
     */
    
    function condition (array $condition) 
    {
        $this->shemeCall->conditionExample($condition);
    }
    
     function where(array $conditions)
    {
       return $this->shemeCall->whereExample($conditions);
    }
    
    function orderBy(array $conditions)
    {
       return $this->shemeCall->orderByExample($conditions);
    }
    
    function groupBy(array $conditions)
    {
       return $this->shemeCall->groupByExample($conditions);
    }
    
    function whereOr(array $conditions)
    {
       return $this->shemeCall->whereOrExample($conditions);
    }
    /*
     * Функция возращает полученный запрос в виде строки
     * @return string
     */   
    function get()
    {
       $this->query = $this->shemeCall->getExample();
       $this->query = preg_replace('/[$;:\/\\\]/','',$this->query); //Проверка на наличие спецзнаков
       $this->query = trim($this->query);
       $this->query = strval($this->query);
       return $this->query;
    }
    
    
    /*
     * Осуществляем подключение
     * и выполнение запроса
     */
    function executeQuery ()
    {
        if ($this->host === null or $this->login === null or $this->password === null or $this->database === null) {
            echo 'Введите парметры подключения';
            die();
        }
        if ($this->query ===  null) {
            echo 'Введите запрос';
            die();
        }
        
        $query = $this->shemeCall->getExample();
        $link = new PDO("$this->host; dbname=$this->database", $this->login, $this->password);
        $result = $link->query($query);
        $this->result = $result;
        return $result;
    }
    
    /*
     * Выводит результаты запроса в читаемом виде
     * 
     */
     public function readResult()
    {
         if ($this->result === TRUE) {
             echo 'Запрос выполнен успешно';
             die();
         }
         if (is_array($this->result)) {
         foreach ($this->result as $row=>$value)
          {  foreach ($value as $row1=>$value1)
            echo "$row1=>$value1";
            
          }
         }
    }
    
    /*
     * Обработка непредусмотренных вызываемых функций
     * 
     */
    public function __call($name, $params) 
    {
        if ($this->shemeCall === null){
            PHP_EOL;
            echo 'Инициализируйте команду смотри HELP</br>';
            PHP_EOL;
            return;
        }
        
        if (!method_exists($this->shemeCall, $name)) {
            PHP_EOL;
            echo 'В рамках класса '. get_class($this->shemeCall).' метод '.$name.' не предусмотрен';
            PHP_EOL;
            return;
            } 
       $this->shemeCall->$name($params);
        
    }
}
$test = new SQLBuilder();
$test->insert('users');
$test->condition(['name=Test','password = 123']);
$test->setQuerySettings('mysql:host=127.0.0.1','chat','root','');
echo $test->get();
$test->executeQuery();
