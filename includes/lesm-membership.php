<?php

class Lesm_Membership {

    public $default_role = 'subscriber';
    public $member_role = 'member';
    public $student_role = 'student';
    public $initiate_role = 'initiate';
    public $associate_role = 'associate';
    public $administrator_role = 'administrator';

    /**Private meta key access to this class only */
    private $payment_date_meta_key = 'payment-date';
    private $membership_expired_date_meta_key = 'membership-expired-date';
    private $membership_expired_reason_meta_key = 'membership-expired-reason';


    public function __construct() {
        add_action('init', array($this, 'lesm_register_members_role'));
        add_filter('body_class', array($this, 'add_member_body_class'));

        /**
         * Schedule the daily member expiration check
         */
        add_action('init', array($this, 'lesm_schedule_member_expiration_check'));
        add_action('lesm_check_member_expiration', array($this, 'lesm_check_member_expiration_callback'));
    }

    // registering the user roles needed for this website
    public function lesm_register_members_role() {
        $role = get_role($this->member_role);
        if (!$role) {
            $role = add_role($this->member_role, 'Member', array(
                'read' => true,
                'edit_posts' => false,
                'edit_others_posts' => false,
                'delete_posts' => false,
            ));
        }
        $student_role = get_role($this->student_role);
        if (!$student_role) {
            $student_role = add_role($this->student_role, 'Student', array(
                'read' => true,
                'edit_posts' => false,
                'edit_others_posts' => false,
                'delete_posts' => false,
            ));
        }
        $initiate_role = get_role($this->initiate_role);
        if (!$initiate_role) {
            $initiate_role = add_role($this->initiate_role, 'Initiate', array(
                'read' => true,
                'edit_posts' => false,
                'edit_others_posts' => false,
                'delete_posts' => false,
            ));
        }
        $associate_role = get_role($this->associate_role);
        if (!$associate_role) {
            $associate_role = add_role($this->associate_role, 'Associate', array(
                'read' => true,
                'edit_posts' => false,
                'edit_others_posts' => false,
                'delete_posts' => false,
            ));
        }
    }

    public function lesm_handle_membership_payment_date() {
        $user = wp_get_current_user();
        $user_id = $user->ID;
    
        $payment_date = get_user_meta($user_id, $this->payment_date_meta_key, true);
        $next_payment_date = date('Y-12-31');
    
        $current_year = date('Y');
        $payment_year = $payment_date ? date('Y', strtotime($payment_date)) : null;
    
        // $available_roles_to_check_payment = array('student', 'member', 'initiate', 'associate');
        // if (array_intersect($available_roles_to_check_payment, (array) $user->roles)) {
        // }
        if($payment_date && $payment_year == $current_year){
            $remaining_membership_days = (strtotime($next_payment_date) - strtotime($payment_date)) / (60 * 60 * 24);
        } else {
            $remaining_membership_days = 0;
        }
    
        return $remaining_membership_days;
    }

    // this filter will add member class to body tag so that we can hide non member element
    public function add_member_body_class($classes) {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $roles_to_add_class = array($this->student_role, $this->member_role, $this->initiate_role, $this->associate_role, $this->administrator_role);
            $user_roles = (array) $user->roles;
            if (!empty($user_roles) && array_intersect($roles_to_add_class, $user_roles)) {
                $classes[] = 'member';
            }
        }
    
        return $classes;
    }


    public function lesm_schedule_member_expiration_check(){
        if (!wp_next_scheduled('lesm_check_member_expiration')) {
            wp_schedule_event(time(), 'hourly', 'lesm_check_member_expiration');
        }
    }

    public function lesm_check_member_expiration_callback()
    {
        $current_date = current_time('Y-m-d'); // full date, WP timezone
        $current_year = date('Y', strtotime($current_date));

        $expiration_date = $current_year . '-12-31';
    
        // Only run on December 31st
        if ($current_date !== $expiration_date) {
            return;
        }
    
        // Get all users with member roles
        $member_roles = array($this->member_role, $this->student_role, $this->initiate_role, $this->associate_role);
        $expired_users = array();
    
        foreach ($member_roles as $role) {
            $users = get_users(array('role' => $role));
    
            foreach ($users as $user) {
                $payment_date = get_user_meta($user->ID, $this->payment_date_meta_key, true);
                $payment_year = $payment_date ? date('Y', strtotime($payment_date)) : null;
    
                // If user hasn't paid for current year, remove their role
                 if (!$payment_date || $payment_year != $current_year) {
                    $user->remove_role($role);
                    $user->add_role($this->default_role); // Default role
    
                    // Log the expiration
                    update_user_meta($user->ID, $this->membership_expired_date_meta_key, $current_date);
                    update_user_meta($user->ID, $this->membership_expired_reason_meta_key, 'Annual expiration - no payment for ' . $current_year);
    
                    $expired_users[] = array(
                        'user_id' => $user->ID,
                        'user_email' => $user->user_email,
                        'role' => $role,
                        'payment_date' => $payment_date
                    );
                 }
            }
        }
    
        // Log the expiration process
        if (!empty($expired_users)) {
            // Get existing logs (if any)
            $logs = get_option('lesm_expiration_logs', array());

            // Append new log entry
            $logs[] = array(
                'date'          => $current_date,
                'expired_count' => count($expired_users),
                'expired_users' => $expired_users
            );

            // Save back to option
            update_option('lesm_expiration_logs', $logs);

            // (Optional) also keep the latest one separately if needed
            update_option('lesm_last_expiration_log', end($logs));

            // Send notification email to admin
            $this->lesm_send_expiration_notification($expired_users);
        }
    }

    public function lesm_send_expiration_notification($expired_users){
        $admin_email = get_option('admin_email');
        $site_name = get_bloginfo('name');

        $subject = 'Member Role Expiration Report - ' . $site_name;
        $message = "The following member roles have been expired due to annual expiration:\n\n";

        foreach ($expired_users as $user) {
            $message .= "User ID: {$user['user_id']}\n";
            $message .= "Email: {$user['user_email']}\n";
            $message .= "Role: {$user['role']}\n";
            $message .= "Last Payment: " . ($user['payment_date'] ? $user['payment_date'] : 'None') . "\n\n";
        }

        $message .= "Total expired: " . count($expired_users) . "\n";
        $message .= "Date: " . current_time('Y-m-d H:i:s');

        wp_mail($admin_email, $subject, $message);
    }
    
}