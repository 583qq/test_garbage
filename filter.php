<?php

$test_price_fil = array('min' => "1",
                        'max' => "10000");

$test_size = array('min' => "1",
                   'max' => "10000");

class Filter {
    public $owner;
    public $type;
    public $rooms;
    public $price_fil;
    public $size;
    public $street;

    function __construct($_owner = null, $_type = null, $_rooms = null, $_pricefil = null, $_size = null, $_street = null)
    {
        $this->owner = $_owner;
        $this->type = $_type;
        $this->rooms = $_rooms;
        $this->price_fil = $_pricefil;
        $this->size = $_size;
        $this->street = $_street;
    }

    function apartment_type()
    {
        return ($this->type != null) ? "(Params = '$this->type')" : "";
    }

    function room_quantity()
    {
        if ($this->rooms == null) return "";

        return ($this->rooms >= 4) ? "(Rooms >= '4')" : "(Rooms = '$this->rooms')";
    }

    function apartment_price()
    {
        if ($this->price_fil == null) return "";
        $statement = "(";

        if($this->price_fil['min'] != null)
            $statement += "Price >= ".$this->price_fil['min']."";
        
        if($this->price_fil['max'] != null)
            if($statement != "(")
                $statement += "AND Price <= ".$this->price_fil['max']."";
            else
                $statement += "Price <= ".$this->price_fil['max']."";
        
        return $statement + ")";
    }

    function apartment_size()
    {
        if ($this->size == null) return "";
        $statement = "(";

        if($this->size['min'] != null)
            $statement += "Square >= ".$this->size['min']."";
        
        if($this->size['max'] != null)
            if($statement != "(")
                $statement += "AND Square <= ".$this->size['max']."";
            else
                $statement += "Square <= ".$this->size['max']."";
        
        return $statement + ")";
    } 

    function street_like()
    {
        if($this->street == null) return "";

        return "(HouseName LIKE '".$this->street."')";
    }


    function generate()
    {
        $statement = "";
        if ($this->owner != 'All' || $this->owner != null)
            $statement += "WHERE Owner = '".$this->owner."'";
        else
            $statement += "WHERE 1";

        $queries = array('type' => $this->apartment_type(),
                         'rooms' => $this->room_quantity(),
                         'price' => $this->apartment_price(),
                         'size' => $this->apartment_size(),
                         'street' => $this->street_like()
        );

        $_q2e = false;

        foreach($queries as $q)
        {
            if($q != "")
            {
            $_q2e = true;
            break;
            }
        }

        if(!$_q2e)
            return $statement;

        $statement += " (";
        foreach($queries as $q)
        {
            if($q != "")
                $statement += $q;
            if($q == end(array_keys($queries)))
                break;
            
            $statement += " AND ";
        }

        return $statement + ")";
    }
}

$gen = new Filter('All', null, null, $test_price_fil, $test_size, null);
$sql = "SELECT `ID`, `Owner`, `Params`, `Rooms`, `Square`, `Header`, `HouseName`, `Description`, `Price`, `Currency` FROM `allads`" + $gen->generate();

$query = $pdo -> query($sql);
?>