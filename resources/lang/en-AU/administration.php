<?php

return array(
	// Common strings
	'common_home' => 'Home',
	'common_dashboard' => 'Dashboard',
	'common_email_address' => 'Email address',
    'common_username' => 'Username',
	'common_password' => 'Password',
	'common_cancel' => 'Cancel',
    'common_delete' => 'Delete',
    'common_update' => 'Update',
    'common_go_back' => 'Go back',
    'common_actions' => 'Actions',
    'common_tips' => 'Tips',
    'common_tags' => 'Tags',
	'common_heading_delete_confirm' => 'Delete these records?',
	'common_command_delete_confirm' => 'Delete records',
    'common_entity_type_person' => 'Person',
    'common_entity_type_user' => 'User',
    'common_entity_type_address' => 'Address',
    
	
	// Login text
	'login_heading_login' => 'Login',
	'login_forgot_password' => 'Forgotten your password?',
	'login_welcome_text' => 'Welcome to Tranquility. Please enter your email address and password to get started.',
	'login_reset_password_text' => 'Don\'t worry, it happens to us all the time as well. Just enter your email address, and we\'ll send you instructions on how to reset your password.', 
	'login_reset_password' => 'Reset password',
	'login_remember_me' => 'Remember me',
	'login_logout' => 'Logout',
	
	// People strings
	'people_heading_people' => 'People',
    'people_heading_person' => 'Person',
	'people_heading_create' => 'Add a new person',
    'people_heading_update' => 'Update details for :name',
	'people_name' => 'Name',
    'people_label_title' => 'Title',
    'people_label_first_name' => 'First name',
    'people_label_last_name' => 'Last name',
    'people_label_user_account' => 'User account',
    'people_label_no_user_account' => 'No user account',
    'people_command_create' => 'Create new person record',
    'people_command_update' => 'Update details',
    'people_message_delete_confirmation_single' => 'Are you sure you want to delete the record for :name?',
    'people_message_delete_confirmation_multiple' => 'Are you sure you want to delete the :count records currently selected?',
    'people_message_has_active_user_account' => ':name has had a user account on your site since :registeredDateTime.',
    'people_message_has_suspended_user_account' => ':name has a user account on your site, but it is currently suspended!',
    'people_message_no_user_account' => ':name does not have a user account on your site.',
    
    // User strings
    'users_heading_users_people' => 'User accounts',
    'users_heading_users_record' => 'User account for :name',
    'users_heading_create_user' => 'Create user account',
    'users_label_is_active' => 'Active',
    'users_label_is_logged_in' => 'Logged in',
    'users_label_registered_date' => 'User since',
    'users_label_registered_date_long' => 'User account created date',
    'users_label_timezone' => 'Timezone',
    'users_label_locale' => 'Locale',
    'users_label_account_status' => 'Account status',
    'users_label_security_group' => 'User group',
    'users_label_new_password' => 'New password',
    'users_label_new_password_confirm' => 'Re-type your password to confirm',
    'users_label_use_existing_email_address' => 'Use an existing email address',
    'users_label_create_new_username' => 'Create a new username / email address',
    'users_status_active' => 'Active',
    'users_status_suspended' => 'Suspended',
    'users_tip_text_index' => 'To create a new user account, go to their Person record and create it from there. If the user doesn\'t have a Person record, create it first and then add a user account.',
    'users_command_delete_selected_users' => 'Delete selected users',
    'users_command_logout_selected_users' => 'Logout selected users',
    'users_command_activate_selected_users' => 'Activate selected users',
    'users_command_deactivate_selected_users' => 'Suspend selected users',
    'users_command_delete_single_user' => 'Delete user account',
    'users_command_logout_single_user' => 'Forcibly log out user',
    'users_command_activate_single_user' => 'Activate user account',
    'users_command_deactivate_single_user' => 'Suspend user account',
    'users_command_update_user' => 'Update user account',
    'users_command_change_password' => 'Change password',
    'users_command_back_to_users_list' => 'Back to list of users',
    
    // Address strings
    'address_heading_physical_addresses' => 'Addresses',
    'address_heading_phone_addresses' => 'Phone numbers',
    'address_heading_email_addresses' => 'Email addresses',
    'address_heading_add_new_address' => 'Add address to record',
    'address_heading_add_new_phone' => 'Add new phone number',
    'address_heading_add_new_email' => 'Add new email address',
    'address_heading_update_address' => 'Update address details',
    'address_heading_update_phone' => 'Update phone number',
    'address_heading_update_email' => 'Update email address',
    'address_label_address_type' => 'Address type',
    'address_label_work_address' => 'Business address',
    'address_label_home_address' => 'Home address',
    'address_label_billing_address' => 'Billing address',
    'address_label_delivery_address' => 'Shipping address',
    'address_label_home_phone' => 'Home phone',
    'address_label_mobile_phone' => 'Mobile phone',
    'address_label_work_phone' => 'Work phone',
    'address_label_company_phone' => 'Company phone',
    'address_label_pager_phone' => 'Pager',
    'address_label_fax_phone' => 'Fax',
    'address_label_personal_email' => 'Personal',
    'address_label_work_email' => 'Work',
    'address_label_other_email' => 'Other',
    'address_label_line_1' => 'Address line 1',
    'address_label_line_2' => 'Address line 2',
    'address_label_city' => 'City',
    'address_label_state' => 'State',
    'address_label_postcode' => 'Postcode',
    'address_label_country' => 'Country',
    'address_label_phone_number' => 'Phone number',
    'address_label_email_address' => 'Email address',
    'address_message_no_physical_addresses' => 'We don\'t have any contact details for this person.',
    'address_message_no_phone_addresses' => 'We don\'t have any contact phone numbers for this person.',
    'address_message_no_email_addresses' => 'We don\'t have any email addresses for this person.',
    'address_message_delete_address_confirmation' => 'Are you sure you want to delete this address?',
    'address_command_add_another_address' => 'Add another address',
    'address_command_view_map' => 'View map',
    'address_command_make_primary' => 'Make primary',
    
    // Tagging strings
    'tags_heading_update' => 'Update tags',
    'tags_label_tags' => 'Tags',
    'tags_command_add_tag' => 'Add tag',
    
    // Search strings
    'search_heading_search' => 'Search',
    
    // Settings strings
    'settings_heading_dashboard' => 'Settings'
);