<?php 
function checkTemperature($data)
{
    if($data["value"]>100)
        {
            $data["status"]="HIGH";
            $data["message"]="Fever detected";
        }
        else{ 
             $data["status"]="NORMAL";
        $data["message"]="Temperature normal";
        }
       
return $data;
}

function checkPulse($data)
{

    if ($data["value"] > 100) {
        $data["status"] = "HIGH";
        $data["message"] = "Pulse rate high";
    } elseif ($data["value"] < 60) {
        $data["status"] = "LOW";
        $data["message"] = "Pulse rate low";
    } 
    else{
        $data["status"] = "NORMAL";
        $data["message"] = "Pulse normal";


    }
        
    
return $data;


}

function checkBloodPressure($data)
{
    $bloodpressure = explode("/", $data["value"]);

    $systolic = $bloodpressure[0];
    $diastolic = $bloodpressure[1];

    if ($systolic  > 130 || $diastolic> 90) {
        $data["status"] = "HIGH";
        $data["message"] = "BP high";
    } 
    elseif ($systolic  < 90 || $diastolic < 60) {
        $data["status"] = "LOW";
        $data["message"] = "BP low";
    } 
    else {
         $data["status"] = "NORMAL";
        $data["message"] = "BP normal";

    }
       
    
return $data;
}
?>