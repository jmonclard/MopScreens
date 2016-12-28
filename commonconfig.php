<?php
  /*
  Copyright 2014 Metraware
  
  Licensed under the Apache License, Version 2.0 (the "License");
  you may not use this file except in compliance with the License.
  You may obtain a copy of the License at
  
      http://www.apache.org/licenses/LICENSE-2.0
  
  Unless required by applicable law or agreed to in writing, software
  distributed under the License is distributed on an "AS IS" BASIS,
  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
  See the License for the specific language governing permissions and
  limitations under the License.
  */

    function defineVariableArr($variable, $value1, $value2, $value3 = null, $value4 = null)
    {
      if(($value3 !== null) && ($value4 !== null))
        print $variable." = eval(['".$value1."', '".$value2."', '".$value3."', '".$value4."']);\r\n";
      else
      if($value3 !== null)
        print $variable." = eval(['".$value1."', '".$value2."', '".$value3."']);\r\n";
      else
        print $variable." = eval(['".$value1."', '".$value2."']);\r\n";
    }

    $courses = array(
        "Course1" => array(
            "categories" => array (
                array (
                    "classId" => "1", 
                    "className" => "FAHJ", 
                    "radios" => array(
                        array("radio" => "Radio1",
                            "radioName" => "40%"
                        ),
                        array("radio" => "finish",
                            "radioName" => "fin"
                        )
                    )
                ),
                array (
                    "classId" => "2", 
                    "className" => "FBHJ", 
                    "radios" => array(
                        array("radio" => "Radio1",
                              "radioName" => "47%"
                        ),
                        array("radio" => "finish",
                              "radioName" => "fini"
                        )
                    )
                )
            ),
            "courseName" => "CF MD",
            "competitionId" => "7", 
        ),
        "Course2" => array(
            "categories" => array (
                array (
                    "classId" => "1", 
                    "className" => "A", 
                    "radios" => array(
                        array("radio" => "Radio1",
                              "radioName" => "22%"
                        ),
                        array("radio" => "finish",
                              "radioName" => "end"
                        )
                    )
                )
            ),
            "courseName" => "Finale MD",
            "competitionId" => "5"
        )
    );
?>
