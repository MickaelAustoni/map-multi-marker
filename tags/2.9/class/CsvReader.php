<?php

/**
 * Created by PhpStorm.
 * User: resine
 * Date: 29/01/2017
 * Time: 17:21
 */
class CsvReader {

    static function ConvertCsvToREquest($file, $table, $mapId, $imgId){

        //If file is upload
        if($file != NULL){

            if($file['size'] > 0 && $file['type'] == 'text/csv'){

                //Upload file into server
                move_uploaded_file($file['tmp_name'], $file['name']);

                //Init.
                $reqInsert = null;
                $reqValue = null;
                $line = 1;

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
                            //Slice request VALUES
                            for ($i=0; $i < $numberCol; $i++) {

                                //Open bracket
                                if($i == 0){
                                    $reqValue .= "('$mapId',";
                                }

                                //Add img id to request
                                if($i == 5){
                                    $reqValue .= "'".$imgId['default_desc_img_url']."',"."'".$imgId['default_marker_img_url']."'," ;
                                }

                                //Value
                                $reqValue .= "'" . htmlspecialchars($data[$i], ENT_QUOTES) . "'";

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

                    //Delete te last comma
                    $reqValue = substr($reqValue, 0, -1);

                    //Close file
                    fclose($handle);

                    //Delete the file
                    unlink($file['name']);
                }

                return $reqInsert.$reqValue;

            }
            else{
                throw new Exception('ERROR');
            }
        }

    }

}