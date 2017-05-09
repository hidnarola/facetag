<?php

/**
 * Check_permission Hook Class 
 * Check Admin/Business User is logged in or not on every page 
 * And also checks Admin/Business user have permission or not for particular class
 * @author KU
 */
class Check_permission {

    /**
     * initialize function
     * Checks admin/business user is loggedin or not if not loggedin then redirect to login page
     * Checks admin/business user permission to access controllers
     * @return void
     * */
    function initialize() {
        $CI = & get_instance();
        $admin = $CI->session->userdata('facetag_admin');

        $directory = $CI->router->fetch_directory();
        $controller = $CI->router->fetch_class();
        $action = $CI->router->fetch_method();
        //-- Get directory to check admin/business directory
        if (!empty($directory)) {
            $directory = explode('/', $directory);
            $directory = $directory[0];
        }

        $admin_permission = array('home', 'businesses', 'payments', 'settings', 'orders', 'invoice', 'login', 'logs', 'subscribed_users', 'users', 'hotels');
        $business_permission = array('home', 'icps', 'profile', 'orders', 'login', 'transactions', 'hotels');

        if (!(empty($directory))) {
            //-- If admin/business user is not logged in then redirect to login page with agent referrer set
            if (empty($admin) && $controller != 'login') {

                $redirect = site_url(uri_string());
                if ($directory == 'admin')
                    redirect('admin?redirect=' . base64_encode($redirect));
                else
                    redirect('login?redirect=' . base64_encode($redirect));
            }

            //-- If admin/business user is logged in then check for permission
            //-- Allow access admin controllers to only admin and business controllers to business user

            if (!empty($admin)) {
                if ($admin['user_role'] == 1) { //- If Admin is logged in then
                    if ($controller == 'home' && $directory != 'admin') {
                        $CI->session->set_flashdata('error', 'You are not authorized to access this page');
                        redirect('admin/home');
                    }

                    if (!in_array($controller, $admin_permission)) {
                        $CI->session->set_flashdata('error', 'You are not authorized to access this page');
                        redirect('admin/home');
                    }
                } else if ($admin['user_role'] == 2) { //- If Business Admin is logged in then
                    if ($controller == 'home' && $directory != 'business') {
                        $CI->session->set_flashdata('error', 'You are not authorized to access this page');
                        redirect('business/home');
                    }
                    if (!in_array($controller, $business_permission)) {
                        $CI->session->set_flashdata('error', 'You are not authorized to access this page');
                        redirect('business/home');
                    }
                }
            }
            //-- If admin/business user is logged in and access the login page then redirect to home page 
            if (!empty($admin) && $controller == 'login' && $action != 'logout') {

                if ($admin['user_role'] == 1) { //- If Admin is logged in then
                    redirect('admin/home');
                } else if ($admin['user_role'] == 2) { //- If Business Admin is logged in then
                    redirect('business/home');
                }
            }
        } else {
            //-- If Business login page is accessed and business is already logged in then redirect to business home page
            if ($controller == 'login' && $action != 'logout') {
                if ((!empty($admin)) && ($admin['user_role'] == 2))
                    redirect('business/home');
            }
        }
    }

}

?>