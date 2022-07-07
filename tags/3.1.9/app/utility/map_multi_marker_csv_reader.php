<?php


class map_multi_marker_csv_reader
{

    /**
     * INSERT MARKER FROM CSV INTO DATABASE
     * @param $file
     * @param $mapId
     * @param $imgId (array)
     * @return bool
     * @throws Exception
     */
    static function insertCsv($file, $mapId, $imgId)
    {

        global $wpdb;

        $table = $wpdb->prefix . 'mapmarker_marker';
        $placeholder = "INSERT INTO $table(marker_id,titre,description,adresse,telephone,weblink,img_desc_marker,img_icon_marker,latitude,longitude) VALUE( %d, %s, %s, %s, %s, %s, %d, %d, %s, %s )";
        $line = 1;
        $pathCsvFile = dirname(__FILE__) . '/' . $file['name'];


        //If file is upload
        if ($file != NULL) {

            //Extension allow
            $allowFiles = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');

            //If file is valid (csv file)
            if ($file['size'] > 0 && in_array($file['type'], $allowFiles)) {

                //Upload file into server
                move_uploaded_file($file['tmp_name'], $pathCsvFile);

                //Read csv
                if (($handle = fopen($pathCsvFile, "r")) !== FALSE) {

                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                        //Start at line 2
                        if ($line > 1) {

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
                            $query = $wpdb->query(
                                $wpdb->prepare(
                                    $placeholder,
                                    $values
                                )
                            );

                            //If fail query
                            if (!$query) {
                                throw new Exception('ERROR_SQL');
                            }

                        }
                        //increment line
                        $line++;
                    }

                    //Close file
                    fclose($handle);
                    //Delete the file
                    unlink($pathCsvFile);
                }

                return true;

            } else {
                throw new Exception('ERROR_VALID');
            }
        }

    }

}