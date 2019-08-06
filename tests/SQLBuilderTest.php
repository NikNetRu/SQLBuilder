<?php
use PHPUnit\Framework\TestCase;

require_once '..\SQLBuilder.php';
require_once '\vendor\autoload.php';

class SQLBuilderTest extends TestCase
{ 
    function insertTest () 
    {   //Позитивный тесткейс
        $expectedresult = "INSERT INTO MyTable (name,age,sex) VALUES ('Andrey','28','male')";
        $result = new SQLBuilder();
        $result->insert('MyTable');
        $result->condition(['name = Andrey','age = 28', 'sex = male']);
        $query = $result->get();
        $query->assertContains($expectedresult);
        
        //Негативный тесткейс
        $expectedresult = "INSERT INTO MyTable222 (125,0,) VALUES ('','28','male')";
        $result = new SQLBuilder();
        $result->insert('MyTable222');
        $result->condition(['125 = <<','0 = 28', '<> = male']);
        $query = $result->get();
        $query->assertContains($expectedresult); 
    }
    
    function deleteTest () 
    {   //Позитивный тесткейс
        $expectedresult = "DELETE FROM MyTable WHERE name='Andrey' AND age>'28' AND sex!='male' OR colors!='blue' OR flowers!='rose'";
        $expectedresult = trim($expectedresult);
        $result = new SQLBuilder();
        $result->delete('MyTable');
        $result->condition(['name = Andrey','age > 28', 'sex != male']);
        $result->whereOr(['colors != blue', 'flowers != rose']);
        $query = $result->get();
        $query->assertContains($expectedresult);
        
        //Негативный тесткейс
        $expectedresult = "DELETE FROM MyTable WHERE name=<<'' AND 0='28' AND !='male'";
        $result = new SQLBuilder();
        $result->delete('MyTable');
        $result->condition(['name =<<','0 = 28', '<> != male']);
        $query = $result->get();
        $query->assertContains($expectedresult);  
    }

    function SelectTest () 
    {   //Позитивный тесткейс
        $expectedresult = "SELECT name, age, email FROM MyTable WHERE name='Andrey' AND age>'28' AND sex!='male' GROUP BY name, age,family ORDER BY name, age";
        $expectedresult = trim($expectedresult);
        $result = new SQLBuilder();
        $result->select('MyTable');
        $result->condition(['name, age, email']);
        $result->where(['name=Andrey','age>28', 'sex!=male']);
        $result->orderBy(['name, age']);
        $result->groupBy(['name, age','family']);
        $result->get();
        $query = $result->get();
        $query->assertContains($expectedresult);
        
        //Негативный тесткейс
        $expectedresult = "SELECT email, email, emma,, FROM MyTable";
        $expectedresult = trim($expectedresult);
        $result = new SQLBuilder();
        $result->select('MyTable');
        $result->condition(['email, email, emma,,']);
        $query = $result->get();
        $query->assertContains($expectedresult);
          
    }
    
    function updateTest () 
    {   //Позитивный тесткейс
        $expectedresult = "UPDATE MyTable SET name='error37' WHERE name='Andrey' AND age>'28' AND sex!='male'";
        $expectedresult = trim($expectedresult);
        $result = new SQLBuilder();
        $result->update('MyTable');
        $result->condition(['name=error37']);
        $result->where(['name = Andrey','age > 28', 'sex != male']);
        $query = $result->get();
        $query->assertContains($expectedresult);
        
        //Негативный тесткейс
        $expectedresult = "UPDATE table SET cash='99'";
        $result = new SQLBuilder();
        $result->update('table');
        $result->condition(['cash = 99']);
        $query = $result->get();
        $query->assertContains($expectedresult);
    }
}