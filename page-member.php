<?php
/* Template Name: LESM Member Area */

// Redirect if not logged in
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( get_permalink() ) );
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lesm_profile_nonce']) && wp_verify_nonce($_POST['lesm_profile_nonce'], 'lesm_update_profile_action')) {
    // Update core user fields
    wp_update_user([
        'ID'         => $user_id,
        'first_name' => sanitize_text_field($_POST['first_name']),
        'last_name'  => sanitize_text_field($_POST['last_name']),
    ]);

    // Update user meta fields
    update_user_meta($user_id, 'secondary-email', sanitize_email($_POST['secondary_email']));
    update_user_meta($user_id, 'gender', sanitize_text_field($_POST['gender']));
    update_user_meta($user_id, 'mobile', sanitize_text_field($_POST['mobile']));
    update_user_meta($user_id, 'telephone', sanitize_text_field($_POST['telephone']));
    update_user_meta($user_id, 'address', sanitize_text_field($_POST['street_address']));
    update_user_meta($user_id, 'city', sanitize_text_field($_POST['city']));
    update_user_meta($user_id, 'postal-code', sanitize_text_field($_POST['postal_code']));
    update_user_meta($user_id, 'country', sanitize_text_field($_POST['country']));

    // Validate password fields
    $password_error = '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($new_password) || !empty($confirm_password)) {
        if ($new_password !== $confirm_password) {
            $password_error = 'Passwords do not match.';
        } elseif (strlen($new_password) < 6) {
            $password_error = 'Password must be at least 6 characters.';
        }
    }

    // Only proceed to change password and redirect if no error
    if (empty($password_error)) {
        if (!empty($new_password)) {
            wp_set_password($new_password, $user_id);
            wp_set_auth_cookie($user_id);
        }

        // Success: redirect
        wp_redirect(add_query_arg('updated', 'true', get_permalink()));
        exit;
    }
}


// User data
$user_roles = $current_user->roles;
$user_role = !empty($user_roles) ? ucfirst($user_roles[0]) : 'Subscriber';

$user_email       = $current_user->user_email;
$user_registered  = $current_user->user_registered;
$display_name     = $current_user->display_name;
$payment_date     = get_user_meta($user_id, 'payment-date', true);
$gender           = get_user_meta($user_id, 'gender', true);
$mobile           = get_user_meta($user_id, 'mobile', true);
$telephone        = get_user_meta($user_id, 'telephone', true);
$secondary_email  = get_user_meta($user_id, 'secondary-email', true);
$street_address   = get_user_meta($user_id, 'address', true);
$city             = get_user_meta($user_id, 'city', true);
$postal_code      = get_user_meta($user_id, 'postal-code', true);
$country          = get_user_meta($user_id, 'country', true);


get_header();
?>

<div class="lesm-member-area-wrapper">
    <div class="lesm-member-area-details">
        <?php if ( isset($_GET['updated']) && $_GET['updated'] === 'true' ) : ?>
            <div class="update-success" style="color: green; margin-bottom: 20px;">Profile details updated successfully!</div>
        <?php endif; ?>

        <?php if (!empty($password_error)) : ?>
            <div class="update-error" style="color: red; margin-bottom: 20px;"><?php echo esc_html($password_error); ?></div>
        <?php endif; ?>


        <form method="post" action="" class="lesm-member-profile-form">
            <?php wp_nonce_field('lesm_update_profile_action', 'lesm_profile_nonce'); ?>
            <div class="lesm-member-details-wrapper">
                <!-- Left: Main User Info -->
                <div class="lesm-member-details-left">
                    <h4>Your Information</h4>
                    <div class="details-item">
                        <div class="item-title"><span class="label">Username</span></div>
                        <input type="text" class="input-field" readonly name="username" value="<?php echo esc_attr($current_user->user_login); ?>" />
                    </div>
                    <div class="details-item">
                        <div class="item-title"><span class="label">First Name</span></div>
                        <input type="text" class="input-field" name="first_name" value="<?php echo esc_attr($current_user->user_firstname); ?>" />
                    </div>
                    <div class="details-item">
                        <div class="item-title"><span class="label">Last Name</span></div>
                        <input type="text" class="input-field" name="last_name" value="<?php echo esc_attr($current_user->user_lastname); ?>" />
                    </div>
                    <div class="details-item">
                        <div class="item-title"><span class="label">Account Email</span></div>
                        <input type="email" class="input-field" readonly name="user_email" value="<?php echo esc_attr($user_email); ?>" />
                    </div>
                    <div class="details-item">
                        <div class="item-title"><span class="label">Secondary Email</span></div>
                        <input type="email" class="input-field" name="secondary_email" value="<?php echo esc_attr($secondary_email); ?>" />
                    </div>
                    <div class="details-item">
                        <div class="item-title"><span class="label">Your Level</span></div>
                        <input type="text" class="input-field" name="user_role" value="<?php echo esc_attr($user_role); ?>" readonly />
                    </div>
                    <div class="details-item">
                        <div class="item-title"><span class="label">Gender</span></div>
                        <select class="input-field" name="gender">
                            <option value="male" <?php selected(strtolower($gender), 'male'); ?>>Male</option>
                            <option value="female" <?php selected(strtolower($gender), 'female'); ?>>Female</option>
                        </select>
                    </div>
                    <div class="details-item">
                        <div class="item-title"><span class="label">Mobile</span></div>
                        <input type="text" class="input-field" name="mobile" value="<?php echo esc_attr($mobile); ?>" />
                    </div>
                    <div class="details-item">
                        <div class="item-title"><span class="label">Telephone</span></div>
                        <input type="text" class="input-field" name="telephone" value="<?php echo esc_attr($telephone); ?>" />
                    </div>
                </div>

                <!-- Address Details -->
                <div class="lesm-member-address">
                    <div class="your-address">
                        <h4>Your Address</h4>
                        <div class="details-item">
                            <div class="item-title"><span class="label">Street Address</span></div>
                            <input type="text" class="input-field" name="street_address" value="<?php echo esc_attr($street_address); ?>" />
                        </div>
                        <div class="details-item">
                            <div class="item-title"><span class="label">City</span></div>
                            <input type="text" class="input-field" name="city" value="<?php echo esc_attr($city); ?>" />
                        </div>
                        <div class="details-item">
                            <div class="item-title"><span class="label">Postal Code</span></div>
                            <input type="text" class="input-field" name="postal_code" value="<?php echo esc_attr($postal_code); ?>" />
                        </div>
                        <div class="details-item">
                            <div class="item-title"><span class="label">Country</span></div>
                            <input type="text" class="input-field" name="country" value="<?php echo esc_attr($country); ?>" />
                        </div>
                    </div>

                    <div class="lesm-member-details-right other-details">
                        <h4>Membership Details</h4>
                        <div class="details-item">
                            <div class="item-title"><span class="label">Registered On</span></div>
                            <div class="text-field"><?php echo esc_html(date('F j, Y', strtotime($user_registered))); ?></div>
                        </div>
                        <?php
                        // Check if payment_date exists and is for the current year
                        $current_year = date('Y');
                        $payment_year = $payment_date ? date('Y', strtotime($payment_date)) : null;

                        if ($payment_date && $payment_year == $current_year) { ?>
                        <div class="details-item">
                            <div class="item-title"><span class="label">Last Payment Date</span></div>
                            <div class="text-field"><?php echo esc_html(date('F j, Y', strtotime($payment_date))); ?></div>
                        </div>
                        <div class="details-item">
                            <div class="item-title"><span class="label">Next Payment Date</span></div>
                            <div class="text-field"><?php echo esc_html(date('F j, Y', strtotime($current_year . '-12-31'))); ?></div>
                        </div>
                        <div class="details-item">
                            <div class="item-title"><span class="label">Remaining Membership Days</span></div>
                            <div class="text-field"><?php echo esc_html(lesm_handle_membership_payment_date()); ?></div>
                        </div>
                        <?php 
                        } else { ?>
                        <div class="details-item">
                            <div class="item-title"><span class="label">Membership Status</span></div>
                            <div class="text-field">Your subscription is expired</div>
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <h4>Change Password (optional)</h4>
            <div class="details-item">
                <div class="item-title"><span class="label">New Password</span></div>
                <input type="password" class="input-field" name="new_password" autocomplete="off" />
            </div>
            <div class="details-item">
                <div class="item-title"><span class="label">Confirm New Password</span></div>
                <input type="password" class="input-field" name="confirm_password" autocomplete="off" />
            </div>
            <div class="button-container" style="margin-top: 20px;">
                <a href="<?php echo esc_url(wp_logout_url(home_url())); ?>" class="button logout">Log Out</a>
                <button type="submit" class="button edit-profile">Update Profile</button>
            </div>
        </form>
    </div>
</div>

<?php get_footer(); ?>
