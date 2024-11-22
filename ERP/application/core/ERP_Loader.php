<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ERP_Loader extends CI_Loader {

    // Custom method to load services
    public function service($service_name): void
    {
        // Define the full class name with namespace
        $service_class = 'App\\Src\\Services\\' . ucfirst($service_name);

        // Check if the class exists (Composer will autoload it)
        if (class_exists($service_class)) {
            // Instantiate the service class
            $CI =& get_instance();
            $CI->$service_name = new $service_class();
        } else {
            show_error('Unable to load the requested service: ' . $service_name);
        }
    }
}
