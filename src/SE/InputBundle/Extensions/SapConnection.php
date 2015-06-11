<?php

namespace SE\InputBundle\Extensions;

use SE\InputBundle\Extensions\SapConfig;
use SE\InputBundle\Extensions\Spyc;
use SE\InputBundle\Entity\SAPRF;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

extension_loaded("sapnwrfc");
global $SAP_CONFIG;

class SapConnection
{
    public function setUp() {
        global $SAP_CONFIG;
        $this->config = Spyc::YAMLLoad($SAP_CONFIG);
    }

    public function sapConnect() {
        try {
            $this->conn = new sapnwrfc($this->config);
        }
        catch (sapnwrfcConnectionException $e) {
            echo "Exception type: ".$e."<br />";
            echo "Exception key: ".$e->key."<br />";
            echo "Exception code: ".$e->code."<br />";
            echo "Exception message: ".$e->getMessage();
        }
        catch (Exception $e) {
            echo "Exception type: ".$e."\n";
            echo "Exception key: ".$e->key."\n";
            echo "Exception code: ".$e->code."\n";
            echo "Exception message: ".$e->getMessage();
            throw new Exception('Connection failed.');
        }    
    }

    public function readTable(){
        try {
            $func = $this->conn->function_lookup("RFC_READ_TABLE");
            $parms = array('QUERY_TABLE' => "LTAP",
                           //'ROWCOUNT' => 10,
                           'DELIMITER' => "@",
                           'FIELDS' => array(array('FIELDNAME' => "LGNUM"),
                                             array('FIELDNAME' => "TANUM"),
                                             array('FIELDNAME' => "MATNR"),
                                             array('FIELDNAME' => "WERKS"),
                                             array('FIELDNAME' => "QDATU"),
                                             array('FIELDNAME' => "QZEIT"),
                                             array('FIELDNAME' => "QNAME"),
                                             array('FIELDNAME' => "VLTYP")
                                             ),
                           'OPTIONS' => array(array('TEXT' => "LGNUM EQ 'L79' AND QDATU EQ '20150610'")
                                              ));
            $results = $func->invoke($parms);
            
            $datas = array(0 => $results["DATA"], 1 => $results["FIELDS"], 2 => $parms);
            return $datas;
        }
        catch (sapnwrfcCallException $e) {
            echo "Exception type: ".$e."\n";
            echo "Exception key: ".$e->key."\n";
            echo "Exception code: ".$e->code."\n";
            echo "Exception message: ".$e->getMessage()."\n";
        }
        catch (Exception $e) {
            echo "Exception type: ".$e."\n";
            echo "Exception key: ".$e->key."\n";
            echo "Exception code: ".$e->code."\n";
            echo "Exception message: ".$e->getMessage()."\n";
            throw new Exception('The function module failed.');
        }
    }

    public function saveTable($data, ObjectManager $manager){

        for($i=0; $i<sizeof($data[0]); $i++){
            //We have use the / symbol as a delimiter, so we need to cut every field and put it on an array slot
            $cell = split("@",$data[0][$i]["WA"]);

            $import = new SAPRF();
            $import->setWarehouse($cell[0]);
            $import->setTransferOrder($cell[1]);
            $import->setMaterial($cell[2]);
            $import->setPlant($cell[3]);
            $date = date_create_from_format('Ymd', $cell[4]);
            $time = date_create_from_format('His', $cell[5]);
            $import->setDateConfirmation($date);
            $import->setTimeConfirmation($time);
            $import->setUser($cell[6]);
            $import->setSourceStorageType($cell[7]);

            $manager->persist($import);
        }

        $manager->flush();
    }

    public function sapClose(){
    //release the function and close the connection
       $this->conn->close();
    }
}