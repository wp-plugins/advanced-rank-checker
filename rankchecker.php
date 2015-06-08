<?php
/**
 * Plugin Name: Advanced Rank Checker
 * Plugin URI: https://wordpress.org/plugins/advanced-rank-checker/
 * Description: Advanced Rank Checker lets you check your keywords ranking
 * Version: 1.3.5
 * Author: Buddy Jansen
 * Author URI: https://wordpress.org/plugins/advanced-rank-checker/
 * License: GPL2
 */


class rankchecker {
        
    /*
     * Add constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array($this, 'register_menu_item' ));
        add_action( 'init', array($this, 'create_post_type'));
        add_action( 'admin_init', array($this, 'register_scripts'));
        add_action( 'admin_init', array($this, 'register_styles'));
        add_action( 'publish_rankchecker', array($this, 'addpostmeta'));
        add_action( 'admin_notices', array($this, 'country_notice'));
    }
    
    /*
     * Register style scripts
     */
    public function register_styles() {
        if ($_GET['page'] == 'rank_checker' || $_GET['page'] == 'rankchecker_options') {
             wp_register_style( 'bootstrap', plugins_url('assets/css/bootstrap.min.css', __FILE__) );
             wp_register_style( 'animate', plugins_url('assets/css/animate.css', __FILE__) );
             wp_register_style( 'rankchecker', plugins_url('rankchecker.css', __FILE__) );
             wp_enqueue_style( 'bootstrap' );
             wp_enqueue_style( 'animate' );
             wp_enqueue_style( 'rankchecker' );
        }

        wp_register_style( 'rc_dashboard', plugins_url('assets/css/dashboard.css', __FILE__) );
        wp_enqueue_style( 'rc_dashboard' );
    }
    
    /*
     * Register js scripts
     */
    public function register_scripts() {
        if ($_GET['page'] == 'rank_checker' || $_GET['page'] == 'rankchecker_options') {
            wp_enqueue_script('//code.jquery.com/jquery-1.11.3.min.js');
            wp_enqueue_script('//code.jquery.com/jquery-migrate-1.2.1.min.js');
            wp_enqueue_script('bootstrapjs', plugins_url('assets/js/bootstrap.min.js', __FILE__));
            wp_enqueue_script('customjs', plugins_url('assets/js/customjs.js', __FILE__));
        }
    }
    
    /*
	* Notice for country setup
	*/
	public function country_notice() {
            $options = get_option('rankchecker_settings');
            if($options['rankchecker_select_field_0'] == '0' || empty($options['rankchecker_select_field_0'])) {
                echo '<div class="error"><h1>IMPORTANT!</h1>';
                echo '<p>You have to setup the country of your Google searches first before you continue.<br><a href="'.get_site_url().'/wp-admin/admin.php?page=rankchecker_options">Click here</a> to set the default search country.</p></div>';
            }
	}
    
    /*
     * Create custom post type
     */
    public function create_post_type() {
        $labels = array(
            'name'               => _x( 'Keywords', 'post type general name' ),
            'singular_name'      => _x( 'Keyword', 'post type singular name' ),
            'add_new'            => _x( 'Add New', 'keyword' ),
            'add_new_item'       => __( 'Add New keyword' ),
            'edit_item'          => __( 'Edit keyword' ),
            'new_item'           => __( 'New keyword' ),
            'all_items'          => __( 'All keywords' ),
            'view_item'          => __( 'View keyword' ),
            'search_items'       => __( 'Search keywords' ),
            'not_found'          => __( 'No keywords found' ),
            'not_found_in_trash' => __( 'No keywords found in the Trash' ), 
            'parent_item_colon'  => '',
            'menu_name'          => 'Keywords'
        );
        
        $args = array(
          'labels'        => $labels,
          'description'   => 'Show keywords ranking in Google',
          'public'        => true,
          'menu_position' => 88,
          'supports'      => array( 'title'),
          'has_archive'   => true,
          'menu_icon'     => 'dashicons-admin-site',
        );
        register_post_type('rankchecker', $args);
    }
            
    /*
     * Register menu item for plugin
     */
    public function register_menu_item() {
        add_menu_page( 'Rank Checker', 'Rank Checker', 'manage_options', 'rank_checker', array($this, 'show_ranking'), 'dashicons-admin-site', 89);
    }
    
    // Show ranking of keywords & process
    public function show_ranking($hidecheck) {
	    
        // Set global $wpdb for sql connection
        global $wpdb;
        
        $post = array("post_status"  =>  "publish");
        wp_insert_post($post);
        
        // Get options from options page
        $options = get_option('rankchecker_settings');
 
        // Show table with contents
        echo '<h2>Welcome to the Advanced Rank Checker</h2>';
        echo '<p>You can use this system to check your keywords ranking. You can check each keyword once a day.</p>';
        echo '<p>Did you not add keywords yet? &nbsp;<a href="'.get_site_url().'/wp-admin/edit.php?post_type=rankchecker">Click here</a> to start</p>';
		echo '<div class="show-warning"></div>';
        echo '<div id="show_dashboard" class="postbox">';
        echo '<table class="table table-striped">';
        echo '<thead><tr>';
        if (!$hidecheck == true) {
            echo '<th></th>';
        }
        echo '<th>#</th>';
        echo '<th>Keyword</th>';
        echo '<th>Google Ranking</th>';
        echo '<th>Country</th>';
        echo '<th>Last checked</th>';
        if(!$hidecheck == true) {
            echo '<th>Check</th>';
        }
        echo '</tr></thead><tbody>';
        
        // SQL query to get all posts where meta_key is rankchecker
        $sql = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE 'rankchecker' ORDER BY meta_id DESC");
        $ids = array();
        
        // Get current time
        $datetimenow = time() + 2*60*60;
        
        // Set counter for plussign button
        $count = 0;
        
        // Loop through postmeta
        foreach ($sql as $row) {

            // Meta value in $meta_value
            $meta_value = unserialize($row->meta_value);

            if(in_array($row->post_id, $ids)) {
                
            } else {
                $positions = array();
                
                // Push id to array so it displays only once
                array_push($ids, $row->post_id);
                
                // Get second last position results for keywords
                $sql2 = $wpdb->get_results("SELECT meta_value FROM (SELECT * FROM wp_postmeta ORDER BY meta_id DESC) sub WHERE post_id LIKE $row->post_id AND meta_key LIKE 'rankchecker' ORDER BY meta_id DESC LIMIT 3");
                foreach($sql2 as $test) {
                    $meta_value_info = unserialize($test->meta_value);
                    if($meta_value_info['position'] == 'Not checked yet') {
                        continue;
                    }
                    
                    $positions[] = $meta_value_info['position'];
                }
                
                
                if(count($positions) > 1) {
                    $position_total = $positions[1] - $positions[0];
                } 
                
                if(empty($positions)) {
                    $position_total = 0;
	            }
	            
	            if($positions[0] == 'Not in top 100') {
                    $position_total = 100 - $positions[1];
                }
                
                if($positions[1] == 'Not in top 100') {
	                $position_total = 100 - $positions[0];
                }
                
                if($positions[1] == '') {
	                $position_total = 100 - $positions[0];
                }

                // Set sign and color based on result
                if ($positions[0] > $positions[1]) {
                    $sign = "";
                    $color = "red";
                } else {
	              	$sign = "+";
                    $color = "green";  
                }
                
                
                
                if($positions[1] == '' || $positions[1] == 'Not in top 100') {
                    $sign = "+";
                    $color = "green";
                }
                
                if($positions[0] == 'Not in top 100' && $positions[1] < 100) {
	                $color = "red";
	                $sign = "-";
                }
                
                if($positions[0] == 'Not in top 100' && $positions[1] == 'Not in top 100') {
	                $color = "black";
                }
                
                if($position_total == 0) {
	                $color = "black";
                }

                
                echo '<tr>';
                if(!$hidecheck == true) {
                    echo '<td>';
                            echo '<a class="plussign counter-'.$count.'" data-toggle="collapse" data-parent="#accordion" href="#row-'.$row->post_id.'" aria-expanded="true" aria-controls="collapseOne"><img src="'.get_site_url().'/wp-content/plugins/advanced-rank-checker/assets/images/plusteken.png" width="20" height="20" style="position:relative; top:-2px; left:-2px;"></a>';
                    echo '</td>';
                }
                
                echo '<td>'.$row->post_id.'</td>';
                echo '<td><strong>'.$meta_value['keyword'].'</strong></td>';
                echo '<td>'.$meta_value['position'].'<span class="'.$color.'"> ('.$sign.$position_total.') </span></td>';
                echo '<td>'.$meta_value['country'].'</td>';
                
                if ($meta_value['date'] == 'Not checked yet') {
                    echo '<td>'.$meta_value['date'].'</td>';
                } else {
                    echo '<td>'.date('d/m/Y H:i:s', $meta_value['date']).'</td>';
                }
                
                // Set values for time check
                $timeleft = $datetimenow - $meta_value['date'];
                $day = 24*60*60;
                $timeleft_total = $day - $timeleft;
                
                // check whether 24 hours has passed
                if(!$hidecheck == true) {
                    if($timeleft > $day) {
                        echo '<td><form action="" method="POST"><input type="hidden" name="keyword_id" value="'.$row->post_id.'"><input type="hidden" name="keyword" value="'.$meta_value['keyword'].'"><input type="submit" name="submit" value="Check"></form></td>';
                    } else {
                        echo '<td>'.date("H", $timeleft_total).' hours left</td>';
                    }
                } 
                
                $count++;

                echo '</tr>';
                echo '</tbody>';
                
                if (!$hidecheck == true) {
                ?>
                    <tbody id="row-<?php echo $row->post_id; ?>" class="panel-collapse collapse animated lightSpeedIn" role="tabpanel" aria-labelledby="headingOne">

                    <?php
                    $sql_all_results = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE $row->post_id LIKE post_id AND meta_key LIKE 'rankchecker' ORDER BY meta_id DESC");
                    array_shift($sql_all_results);
                    foreach($sql_all_results as $result) {
                        $meta_results = unserialize($result->meta_value);
                        if($meta_results['position'] == 'Not checked yet') {
                            continue;
                        }
                        echo '<tr>';
                        echo '<td></td>';
                        echo '<td></td>';
                        echo '<td>'.$meta_results['keyword'].'</td>';
                        echo '<td>'.$meta_results['position'].'</td>';
                        echo '<td>'.$meta_results['country'].'</td>';
                        echo '<td>'.date('d/m/Y H:i:s', $meta_results['date']).'</td>';
                        echo '<td></td>';
                        echo '</tr>';  
                    }
                }
        
                ?>

                </tbody>
                <?php
	                
            }        
        }
        echo '</table>';
        
       
        echo '</div>';
        $rankchecker_options = new rankchecker_options();
        $countries = $rankchecker_options->countries(); 
        
        // Form process
        if ($_POST['submit']) {

            $searchquery = $_POST['keyword'];
            $searchurl = $_SERVER['HTTP_HOST'];

            if(!empty($searchquery) && !empty($searchurl)) {

                $query = str_replace(" ","+",$searchquery);
                $query = str_replace("%26","&",$query);

                // How many results to search through
                $total_to_search = 100;

                // The number of hits per page
                $hits_per_page   = 10;

                // Position in Google
                $position      = 0;
                
                // Get options from options page
                $options = get_option('rankchecker_settings');
                
                // Countries
                $rankchecker_options = new rankchecker_options();
                $countries = $rankchecker_options->countries(); 

                for($i=0;$i<$total_to_search;$i+=$hits_per_page) {

                    $filename = "http://www.google.".$countries[$options['rankchecker_select_field_0']]."/search?as_q=$query".
                    "&num=$hits_per_page&hl=en&ie=UTF-8&btnG=Google+Search".
                    "&as_epq=&as_oq=&as_eq=&lr=&as_ft=i&as_filetype=".
                    "&as_qdr=all&as_nlo=&as_nhi=&as_occt=any&as_dt=i".
                    "&as_sitesearch=&safe=images&start=$i";
                    
                    $var = file_get_contents($filename);

                    // split the page code by "<h3 class" which tops each result
                    $fileparts = explode("<h3 class=", $var);
					if(!empty($var)) {
	                    for ($f=1; $f<sizeof($fileparts); $f++) {
	                        $position++;
	
	                        if (strpos($fileparts[$f], $searchurl)) {
	                            add_post_meta( $_POST['keyword_id'], 'rankchecker', array(
	                                'keyword'       =>      $searchquery,
	                                'position'      =>      $position,
	                                'date'          =>      $datetimenow,
	                                'country'	=>	$countries[$options['rankchecker_select_field_0']],
	                            ));
	                            echo("<meta http-equiv='refresh' content='1'>");
	                            break;
	                        }
	
	                        if ($position > '99') {
	                             add_post_meta( $_POST['keyword_id'], 'rankchecker', array(
	                                'keyword'       =>      $searchquery,
	                                'position'      =>      'Not in top 100',
	                                'date'          =>      $datetimenow,
	                                'country'	=>	$countries[$options['rankchecker_select_field_0']],
	                            ));
	                            echo("<meta http-equiv='refresh' content='1'>");
	                            break;
	                        }
	                    }
	                } else {
		                echo '<div class="alert alert-danger">It seems like you checked the Google results too many times. Please try again in an hour! If this error keeps showing, please contact your webhosting master. Your IP may be blocked by the Google Search System.</div>';
	                    break;
	                } 
                }
            } else {
                echo 'Something went wrong, please try again.';
            }
        }
        
    }
    
    // Add postmeta on new post
    public function addpostmeta($post_id) {

        // Get post of lastest created post
        $post = get_post($post_id);
        
        // Get options from options page
        $options = get_option('rankchecker_settings');
        

        // Countries
        $rankchecker_options = new rankchecker_options();
        $countries = $rankchecker_options->countries(); 
        
       // Get all posts where post-type is rankchecker 
       $all_posts = get_posts($args);
       
       if(count($all_posts) > 1) {
            // Unset first element because that is the new post we just created
            unset($all_posts[0]);
       }
       
       // Sort elements again so it starts at 0
       $all_posts = array_values($all_posts);
       
       // Loop through posts to check if the post_title already exists
       foreach($all_posts as $rows) {
           if($rows->post_title == $post->post_title && count($all_posts) > 1) {
               wp_delete_post($post_id);
               ?>
                <script>
                    alert('This keyword already exists.');
                    window.history.back();
                </script>            
                <?php
               exit();
           } else {
                // Add post meta for last keyword
                add_post_meta($post_id, 'rankchecker', array(
                    'keyword'           =>          $post->post_title,
                    'position'          =>          'Not checked yet',
                    'date'              =>          'Not checked yet',
                    'country'		=>          $countries[$options['rankchecker_select_field_0']],
                ));
               
           }
       }

    }
}

$rankchecker = new rankchecker();

$options = get_option('rankchecker_settings');
if ($options['rankchecker_checkbox_field_1'] == 1) {
    require_once 'rankchecker_dashboard.php';
}
require_once 'rankchecker_options.php';