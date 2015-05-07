<?php namespace App\Library;

    
class JsonResponseHelper 
{
    public function returnJson( $status, array $values)
    {        
        $values['status'] = $status;
        return json_encode($values);
    }
    
    public function failJson($reason)
    {
        return $this->returnJson("FAILURE", array(
            'detailedStatus' => $reason
        ));
    }
    
     public function succeedJson($reason)
    {
        return $this->returnJson("SUCCESS", array(
            'detailedStatus' => $reason
        ));
    }
}

?>