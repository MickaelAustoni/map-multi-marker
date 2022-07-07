<?php

/**
 * Created by PhpStorm.
 * User: resine
 * Date: 29/01/2017
 * Time: 17:21
 */
class CsvReader {

    /**
     * READ CSV FILE AND RETURN PREPARED SQL REQUEST
     * @param $file
     * @param $table
     * @param $mapId
     * @param $imgId
     * @return string
     * @throws Exception
     */
    static function ConvertCsvToREquest($file, $table, $mapId, $imgId){

        //If file is upload
        if($file != NULL){

            //Extension allow
            $allowFiles = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');

            //If file is valid (csv file)
            if($file['size'] > 0 && in_array($file['type'],$allowFiles)){

                //Upload file into server
                move_uploaded_file($file['tmp_name'], $file['name']);

                //Init.
                $reqInsert = null;
                $reqValue = null;
                $line = 1;
                $notEmpty = false;

                if (($handle = fopen($file['name'], "r")) !== FALSE) {

                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                        //Count col
                        $numberCol = count($data);

                        //Slice request INSERT INTO
                        if($line == 1){
                            $reqInsert = "INSERT INTO `$table`(`marker_id`,`titre`,`description`,`adresse`,`telephone`,`weblink`,`img_desc_marker`,`img_icon_marker`,`latitude`,`longitude`)";

                            $reqValue = 'VALUES';

                            $line++;
                        }else{
                            $notEmpty = true;

                            //Slice request VALUES
                            for ($i=0; $i < $numberCol; $i++) {

                                //Open bracket and set marker id
                                if($i == 0){
                                    $reqValue .= "('$mapId',";
                                }

                                //Add img id to request
                                if($i == 5){
                                    $reqValue .= "'".$imgId['default_desc_img_url']."',"."'".$imgId['default_marker_img_url']."'," ;
                                }

                                //If data is longitude or latitude
                                if($i == 5 || $i == 6){
                                    //Replace comma by point
                                    $data[$i] = str_replace(',','.', $data[$i]);
                                }

                                //Value converted in html entities
                                $reqValue .= "'" . htmlspecialchars($data[$i], ENT_QUOTES | ENT_IGNORE, "UTF-8") . "'";

                                //Comma between value
                                if($i < $numberCol-1){
                                    $reqValue .= ',';
                                }

                                //Close bracket
                                if($i == $numberCol-1){
                                    $reqValue .= '),';
                                }

                            }
                            $line++;
                        }
                    }

                    //Check if value slice request is empty
                    if($notEmpty == true){
                        //Delete te last comma
                        $reqValue = substr($reqValue, 0, -1);
                    }else{
                        throw new Exception('ERROR_EMPTY');
                    }

                    //Close file
                    fclose($handle);

                    //Delete the file
                    unlink($file['name']);
                }

                //Return prepared request
                return $reqInsert.$reqValue;

            }
            else{
                throw new Exception('ERROR_VALID');
            }
        }

    }

}