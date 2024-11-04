<?php
/*
Plugin Name: Custom Maintenance Plugin
Description: This plugin is for wordspring hackathon from the side of tech titans
Version: 1.0.0
Author: Tech Titans
*/

if (! defined('ABSPATH')) exit;

// Hook into admin menu to add the settings page
add_action('admin_menu', 'custom_maintenance_add_admin_menu');

function custom_maintenance_add_admin_menu()
{
    add_menu_page(
        'Custom Maintenance', // Page title
        'Maintenance Mode',    // Menu title
        'manage_options',      // Capability
        'custom-maintenance',  // Menu slug
        'custom_maintenance_settings_page', // Callback function
        'dashicons-admin-tools', // Icon
        100                    // Position
    );
}

// Register settings
add_action('admin_init', 'custom_maintenance_register_settings');

function custom_maintenance_register_settings()
{
    register_setting('custom_maintenance_settings', 'maintenance_mode_enabled');
    register_setting('custom_maintenance_settings', 'maintenance_title');
    register_setting('custom_maintenance_settings', 'maintenance_heading');
    register_setting('custom_maintenance_settings', 'maintenance_description');
    register_setting('custom_maintenance_settings', 'maintenance_background_image');
    register_setting('custom_maintenance_settings', 'maintenance_video_url');
    register_setting('custom_maintenance_settings', 'maintenance_video_start');
    register_setting('custom_maintenance_settings', 'maintenance_video_end');
    register_setting('custom_maintenance_settings', 'maintenance_heading_color');
    register_setting('custom_maintenance_settings', 'maintenance_description_color');
    register_setting('custom_maintenance_settings', 'login_sidebar_enabled');
}


function custom_maintenance_settings_page()
{
?>
    <div class="wrap">
        <h1>Custom Maintenance Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('custom_maintenance_settings');
            do_settings_sections('custom_maintenance_settings');
            $heading = get_option('maintenance_heading');
            if (empty($heading)) {
                $heading = 'This site is under construction';
            }

            $description = get_option('maintenance_description');
            if (empty($description)) {
                $description = 'This site is under construction';
            }

            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Enable Maintenance Mode</th>
                    <td><input type="checkbox" name="maintenance_mode_enabled" value="1" <?php checked(1, get_option('maintenance_mode_enabled'), true); ?> /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Site Title</th>
                    <td><input type="text" name="maintenance_title" value="<?php echo esc_attr(get_option('maintenance_title')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Heading</th>
                    <td>
                        <input type="text" name="maintenance_heading" value="<?php echo esc_attr($heading); ?>" />
                        <label>Color:</label>
                        <input type="color" name="maintenance_heading_color" value="<?php echo esc_attr(get_option('maintenance_heading_color', '#333333')); ?>" class="color-field" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Description</th>
                    <td>
                        <textarea name="maintenance_description" rows="5" cols="50"><?php echo esc_textarea($description); ?></textarea>
                        <label>Color:</label>
                        <input type="color" name="maintenance_description_color" value="<?php echo esc_attr(get_option('maintenance_description_color', '#333333')); ?>" class="color-field" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Background Image URL <small>(Leave empty if using a YouTube video)</small></th>
                    <td><input type="text" name="maintenance_background_image" value="<?php echo esc_attr(get_option('maintenance_background_image')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">YouTube Video URL <small>(Leave empty if using an image)</small></th>
                    <td><input type="text" name="maintenance_video_url" value="<?php echo esc_attr(get_option('maintenance_video_url')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Video Start Time <small>(in seconds)</small></th>
                    <td><input type="number" name="maintenance_video_start" value="<?php echo esc_attr(get_option('maintenance_video_start', 0)); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Video End Time <small>(in seconds, optional)</small></th>
                    <td><input type="number" name="maintenance_video_end" value="<?php echo esc_attr(get_option('maintenance_video_end', 0)); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Login Sidebar</th>
                    <td><input type="checkbox" name="login_sidebar_enabled" value="1" <?php checked(1, get_option('login_sidebar_enabled'), true); ?> /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <style>
        .form-table td label {
            margin-left: 15px;
            margin-right: 5px;
        }

        .color-field {
            vertical-align: middle;
        }
    </style>
<?php
}

add_action('template_redirect', 'custom_maintenance_mode');

function custom_maintenance_mode()
{
    if (get_option('maintenance_mode_enabled') && !current_user_can('manage_options')) {
        // Get settings
        $title = get_option('maintenance_title', 'We\'ll Be Back Soon');
        $heading = get_option('maintenance_heading', 'Under Construction');
        $description = get_option('maintenance_description', 'Our website is currently undergoing scheduled maintenance.');
        $background_image = get_option('maintenance_background_image');
        $video_url = get_option('maintenance_video_url');
        $video_start = intval(get_option('maintenance_video_start', 0));
        $video_end = intval(get_option('maintenance_video_end', 0));
        $heading_color = get_option('maintenance_heading_color', '#333333');
        $description_color = get_option('maintenance_description_color', '#333333');

        $show_login_sidebar = get_option('login_sidebar_enabled'); // Check if the sidebar is enabled
        echo "<!DOCTYPE html><html><head><title>{$title}</title>";
        echo "<style>
                body {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    height: 100vh;
                    margin: 0;
                    background: #f3f3f3;
                    font-family: sans-serif;
                    color: #333;
                }
                .content {
                    text-align: center;
                }
                h1 {
                    font-size: 50px;
                    color: {$heading_color};
                    margin-bottom: 20px;
                }
                p {
                    font-size: 20px;
                    color: {$description_color};
                }
                .background {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: -1;
                    overflow: hidden;
                }
                .background iframe {
                    width: 100vw;
                    height: 100vh;
                    pointer-events: none;
                }
              </style></head><body>";

        if ($video_url) {
            // Trim whitespace and parse the video URL to ensure consistency
            $video_url = trim($video_url);

            // Updated regex to match various YouTube URL formats
            preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:watch\?v=|embed\/|v\/|.+\?v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $video_url, $matches);

            if (!empty($matches[1])) {
                $video_id = $matches[1];

                // Build the embed URL
                $youtube_embed_url = "https://www.youtube.com/embed/{$video_id}?autoplay=1&loop=1&playlist={$video_id}&controls=0&showinfo=0&modestbranding=1&rel=0&mute=1&playsinline=1";

                if ($video_start) $youtube_embed_url .= "&start={$video_start}";
                if ($video_end) $youtube_embed_url .= "&end={$video_end}";

                echo "<div class='background'>
                            <iframe src='{$youtube_embed_url}' frameborder='0' allow='autoplay; loop; mute; fullscreen' allowfullscreen></iframe>
                          </div>";
            } else {
                echo "Invalid video URL.";
            }
        } elseif ($background_image) {
            echo "<div class='background' style='background: url({$background_image}) no-repeat center center fixed; background-size: cover;'></div>";
        }

        echo "<div class='content'>
                <h1>{$heading}</h1>
                <p>{$description}</p>";


        if ($show_login_sidebar) {
            echo "
                        <link rel='stylesheet' href='https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0' />
                        <link rel='preconnect' href='https://fonts.googleapis.com'>
                        <link rel='preconnect' href='https://fonts.gstatic.com' crossorigin>
                
                        <div class='main'>
                            <div class='check'>
                                <input type='checkbox' name='check' id='check'>
                                
                                <!-- Hamburger Icon -->
                                <div class='menu1'>
                                    <label for='check'>
                                        <span class='material-symbols-outlined'>menu</span>
                                    </label>
                                </div>
                                
                                <!-- Sidebar Menu -->
                                <div class='menu'>
                                    <div class='pickers'>
                                        COLOR PICKER FOR BACKGROUND: <input type='color' name='colorpicker' id='colorpicker'>
                                        COLOR PICKER FOR TEXT: <input type='color' name='colorPicker' id='colorPicker'>
                                    </div>
                                    
                                    <!-- Close Button -->
                                    <label for='check'>
                                        <span id='close' class='material-symbols-outlined'>close</span>
                                    </label>
                                    
                                    <h1>User Login</h1>
                                    
                                    <!-- Login Form -->
                                    <div class='items'>
                                        <form method='post' action='" . esc_url(wp_login_url()) . "'>
                                            <div class='one'>
                                                <span class='material-symbols-outlined'>person</span>
                                                <input placeholder='Enter your E-mail' type='text' name='log' id='email' required>
                                            </div>
                                            <div class='two'>
                                                <span class='material-symbols-outlined'>lock</span>
                                                <input placeholder='Password' type='password' name='pwd' id='password' required>
                                            </div>
                                            <div class='remember'>
                                                <input type='checkbox' name='rememberme' id='remember'>
                                                <label for='remember'>Remember me</label>
                                            </div>
                                            <button type='submit'>Login</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const check = document.getElementById('check');
                                const menu = document.querySelector('.menu');
                                const menuIcon = document.querySelector('.menu1 label');
                                const closeButton = document.getElementById('close');
                
                                menuIcon.addEventListener('click', function () {
                                    check.checked = true; // Show sidebar
                                    menu.style.left = '0'; // Show sidebar
                                });
                
                                // Close button in sidebar
                                closeButton.addEventListener('click', function () {
                                    check.checked = false; // Uncheck checkbox to hide sidebar
                                    menu.style.left = '-20vw'; // Hide sidebar
                                });
                
                                // Hide sidebar when clicking outside (optional)
                                document.addEventListener('click', function (event) {
                                    if (!menu.contains(event.target) && !menuIcon.contains(event.target) && check.checked) {
                                        check.checked = false;
                                        menu.style.left = '-20vw'; // Hide sidebar
                                    }
                                });
                            });
                
                            document.addEventListener('DOMContentLoaded', function () {
                                const color = document.querySelector('#colorpicker');
                                const bg = document.querySelector('.menu');
                                const color1 = document.querySelector('#colorPicker');
                                const bg1 = document.querySelector('#email');
                                const bg2 = document.querySelector('#password');
                
                                color.addEventListener('input', () => {
                                    bg.style.backgroundColor = color.value;
                                });
                
                                color1.addEventListener('input', () => {
                                    const colorValue1 = color1.value;
                                    bg.style.color = colorValue1;
                                    bg1.style.backgroundColor = colorValue1;
                                    bg2.style.backgroundColor = colorValue1;
                                });
                            });
                        </script>
                        <style>
                            * {
                                margin: 0;
                                padding: 0;
                                overflow: hidden;
                            }
                            #check {
                                display: none;
                            }
                
                            .menu {
                                width: 20vw;
                                height: 100vh;
                                background-color: black;
                                z-index: 10;
                                border-radius: 3px;
                                color: white;
                                position: fixed; /* Use fixed position to ensure it stays visible */
                                top: 0;
                                left: -20vw; /* Start off-screen */
                                transition: all 0.5s ease;
                            }
                
                            .menu1 {
                                position: fixed; /* Keep hamburger icon in a fixed position */
                                top: 20px;
                                left: 20px;
                                color: white;
                                font-size: 30px;
                                z-index: 100;
                                cursor: pointer;
                            }
                
                            .menu #close {
                                color: white;
                                position: absolute;
                                top: 20px;
                                right: 20px;
                                cursor: pointer;
                            }
                
                            #check:checked ~ .menu {
                                left: 0; /* Slide in the sidebar when checked */
                            }
                
                            #check:checked ~ .menu1 {
                                display: none; /* Hide the hamburger icon when sidebar is open */
                            }
                
                            .items input {
                                padding: 7px 15px;
                                background-color: rgb(66, 63, 63);
                                border: 2px solid grey;
                                border-radius: 5px;
                                width: 80%;
                            }
                            .items {
                                display: flex;
                                flex-direction: column;
                                gap: 10px;
                                margin-left: 20px;
                                margin-top: 10px;
                            }
                            .one, .two {
                                display: flex;
                                align-items: center;
                                margin-right: 7px;
                                gap: 8px;
                                margin-bottom:10px;
                            }
                            h1 {
                                margin-top: 40px;
                                font-weight: bolder;
                                font-size: 40px;
                            }
                            button {
                                width: 69px;
                                height: 34px;
                                margin-left: 140px;
                                background-color: black;
                                color: white;
                                border: 1px solid white;
                            }
                            .menu .pickers {
                                position: absolute;
                                top: 510px;
                                display: flex;
                                flex-direction: column;
                                margin-left: 20px;
                                gap: 10px;
                            }
                            .pickers input {
                                border-radius: 30%;
                            }
                            .remember {
                                display: flex;
                                width: 140px;
                            }
                            .remember #remember {
                                width: 16px;
                                margin-right: 10px;
                                margin-left: 5px;
                            }
                        </.style>";
        }


        echo "</div>";

        echo "</body></html>";
        exit();
    }
}


add_action('init', 'register_custom_maintenance_post_type');

function register_custom_maintenance_post_type()
{
    register_post_type('maintenance_configuration', array(
        'labels' => array(
            'name' => __('Maintenance Configurations'),
            'singular_name' => __('Maintenance Configuration')
        ),
        'public' => false,
        'show_ui' => true,
        'capability_type' => 'post',
        'supports' => array('title'),
        'menu_icon' => 'dashicons-admin-tools',
    ));
}
