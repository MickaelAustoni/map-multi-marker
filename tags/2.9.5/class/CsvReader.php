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
     * @param $mapId
     * @param $imgId (array)
     * @return bool
     * @throws Exception
     */
    static function insertCsv($file, $mapId, $imgId){

        global $wpdb;

        $table = $wpdb->prefix.'mapmarker_marker';
        $placeholder = "INSERT INTO $table(marker_id,titre,description,adresse,telephone,weblink,img_desc_marker,img_icon_marker,latitude,longitude) VALUE( %d, %s, %s, %s, %s, %s, %d, %d, %s, %s )";
        $line = 1;

        //If file is upload
        if($file != NULL){

            //Extension allow
            $allowFiles = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');

            //If file is valid (csv file)
            if($file['size'] > 0 && in_array($file['type'],$allowFiles)){

                //Upload file into server
                move_uploaded_file($file['tmp_name'], $file['name']);

                //Read csv
                if (($handle = fopen($file['name'], "r")) !== FALSE) {

                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                        //Start at line 2
                        if($line > 1){

                            //Store value in array
                            $values = array(
                                $mapId,
                                htmlspecialchars($data[0], ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                htmlspecialchars($data[1], ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                htmlspecialchars($data[2], ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                htmlspecialchars($data[3], ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                htmlspecialchars($data[4], ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                $imgId['default_desc_img_url'],
                                $imgId['default_marker_img_url'],
                                htmlspecialchars($data[5], ENT_QUOTES | ENT_IGNORE, "UTF-8"),
                                htmlspecialchars($data[6], ENT_QUOTES | ENT_IGNORE, "UTF-8")
                            );

                            //Prepare secure request
                            $wpdb->query(
                                $wpdb->prepare(
                                    $placeholder,
                                    $values
                                )
                            );

                        }
                        //increment line
                        $line++;
                    }

                    //Close file
                    fclose($handle);
                    //Delete the file
                    unlink($file['name']);
                }

                return true;

            }
            else{
                throw new Exception('ERROR_VALID');
            }
        }

    }

}