<?php

class controller
{

    /**
     * Load view
     */
    public function loadView()
    {

        require_once MapMultiMarker::instance()->plugin_path . 'template/admin/' . MapMultiMarker::instance()->page . '.php';

    }
}