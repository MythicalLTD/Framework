<?php

/*
 * This file is part of MythicalSystemsFramework.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 *
 * You should have received a copy of the MIT License
 * along with this program. If not, see <https://opensource.org/licenses/MIT>.
 */

return [
    // FILE: /errors/*.twig
    'pages_error_404' => 'Page not found',
    'pages_error_500' => 'Internal server error',
    'pages_error_403' => 'Forbidden',
    'pages_error_401' => 'Unauthorized',
    'pages_error_go_back' => 'Go home',
    'pages_error_404_message' => 'The page you are looking for does not exist.',
    'pages_error_500_message' => 'An internal server error occurred.',
    'pages_error_403_message' => 'You do not have permission to access this page.',
    'pages_error_401_message' => 'You are not authorized to access this page.',
    // FILE /auth/register.twig
    'pages_register_title' => 'Register',
    'pages_register_username' => 'Username',
    'pages_register_email' => 'Email',
    'pages_register_password' => 'Password',
    'pages_register_first_and_last_name' => 'Name',
    'pages_register_username_placeholder' => 'Enter your username',
    'pages_register_email_placeholder' => 'Enter your email',
    'pages_register_password_placeholder' => 'Enter your password',
    'pages_register_first_and_last_name_placeholder' => 'Enter your name',
    'pages_register_button' => 'Create account',
    'pages_register_already_have_account' => 'Already have an account?',
    'pages_register_login' => 'Login',
    'pages_register_description' => 'Create an account to access the platform.',
    'pages_register_description_above' => 'Start for free',
    'pages_register_register_to' => 'Register to',
    'pages_register_alert_error' => 'An error occurred while creating your account.',
    'pages_register_please_fill_all_fields' => 'Please fill all fields.',
    'pages_register_first_name_and_last_name_not_valid' => 'The first and last name you entered is not valid.',
    'pages_register_username_not_valid' => 'The username you entered is not valid.',
    'pages_register_email_not_valid' => 'The email you entered is not valid.',
    'pages_register_password_to_small' => 'The password you entered is too small.',
    'pages_register_password_cannot_match' => 'Password cannot match with username or email.',

    // FILE /auth/login.twig
    'pages_login_alert_error' => 'An error occurred while login in your account.',
    'pages_login_title' => 'Login',
    'pages_login_email' => 'Email',
    'pages_login_password' => 'Password',
    'pages_login_email_placeholder' => 'Enter your email',
    'pages_login_password_placeholder' => 'Enter your password',
    'pages_login_button' => 'Login',
    'pages_login_forgot_password' => 'Forgot password?',
    'pages_login_no_account' => 'Don\'t have an account?',
    'pages_login_register' => 'Register',
    'pages_login_description' => 'Login to access the platform.',
    'pages_login_description_above' => 'Welcome back',
    'pages_login_login_to' => 'Login to',
    'pages_login_please_fill_all_fields' => 'Please fill all fields.',
    'pages_login_user_not_found' => 'User not found.',
    'pages_login_user_not_verified' => 'User not verified.',
    'pages_login_user_not_verified_description' => 'Your account is not verified. Please check your email for the verification link.',
    'pages_login_user_banned' => 'Your account has been banned.',
    'pages_login_user_banned_description' => 'Your account has been banned. Please contact the support for more information.',
    'pages_login_user_deleted' => 'Your account has been deleted.',
    'pages_login_user_deleted_description' => 'Your account has been deleted. Please contact the support for more information.',
    'pages_login_user_password_incorrect' => 'The password you entered is incorrect.',
    'pages_login_user_password_incorrect_description' => 'The password you entered is incorrect. Please try again.',

    // FILE /auth/2fa_setup.twig
    'pages_2fa_setup_title' => '2FA Setup',
    'pages_2fa_setup_description' => 'Setup 2FA to secure your account.',
    'pages_2fa_setup_description_above' => 'Secure your account',
    'pages_2fa_setup_failed_key_wrong' => 'The key you entered is wrong.',
    'pages_2fa_setup_failed_title' => 'Failed to setup 2FA',

    // FILE /components/header.twig
    'components_header_search' => 'Type to search..',
    'components_header_notification' => 'Notification',
    'components_header_myprofile' => 'My profile',
    'components_header_settings' => 'Settings',
    'components_header_logout' => 'Logout',
    // FILE /components/page.twig
    'components_page_dashboard' => 'Dashboard',

    // FILE /components/sidebar.twig
    'components_sidebar_dashboard' => 'Dashboard',
    'components_sidebar_menu' => 'Menu',

    // ALERTS
    'alert_title_error' => 'Wooops, something went wrong!',
    'alert_title_success' => 'Huraaaa, everything is fine!',
    'alert_email_verified' => 'Your email has been verified.',
    'alert_csrf_failed' => 'We could not validate your request. Please try again.',
    'alert_captcha_failed' => 'The captcha verification failed. Please try again.',
    'alert_unknown_error' => 'An unknown error occurred. Please try again.',
    'alert_username_exists' => 'The username you entered already exists. Please try again.',
    'alert_email_exists' => 'The email you entered already exists. Please try again.',
    'alert_email_verification_code_does_not_exist' => 'The email verification code does not exist.',
    'alert_2fa_already_setup' => '2FA is already setup on your account.',
    ' ' => '2FA is not setup on your account.',
    'alert_2fa_setup' => '2FA has been successfully setup on your account.',
];
