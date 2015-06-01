<?php

class rankchecker_options {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_submenu'));
        add_action( 'admin_init', array($this, 'rankchecker_settings_init' ));

    }
    
    public function add_submenu() {
        add_submenu_page('rank_checker', 'Rank Checker options', 'Rank Checker options', 'manage_options', 'rankchecker_options', array($this, 'rankchecker_options_page'));
    }
    
    
    public function rankchecker_settings_init(  ) { 

	register_setting( 'pluginPage', 'rankchecker_settings' );

	add_settings_section(
		'rankchecker_pluginPage_section', 
		__( '', 'wordpress' ), 
		array($this, 'rankchecker_settings_section_callback'), 
		'pluginPage'
	);

	add_settings_field( 
		'rankchecker_checkbox_field_0', 
		__( 'URL', 'wordpress' ), 
		array($this, 'rankchecker_checkbox_field_0_render'), 
		'pluginPage', 
		'rankchecker_pluginPage_section' 
	);
    add_settings_field( 
		'rankchecker_checkbox_field_1', 
		__( 'Turn on dashboard display', 'wordpress' ), 
		array($this, 'rankchecker_checkbox_field_1_render'), 
		'pluginPage', 
		'rankchecker_pluginPage_section' 
	);
	add_settings_field( 
		'rankchecker_select_field_0', 
		__( 'Select country to search in', 'wordpress' ), 
		array($this, 'rankchecker_select_field_0_render'), 
		'pluginPage', 
		'rankchecker_pluginPage_section' 
	);
    }
    
    public function countries() {
		
	global $wpdb;
	
	$check_country = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key LIKE 'arc_countries'");
	if(empty($check_country)) {
	add_post_meta('8888', 'arc_countries', array(
		"0"	=>	"Select your country", "1"	=>	"ac", "2"	=>	"ad", "3"	=>	"ae", "4"	=>	"com.af", "5"	=>	"com.ag", "6"	=>	"com.ai", "7"	=>	"al", "8"	=>	"am", "9"	=>	"co.ao",
		"10"	=>	"com.ar", "11"	=>	"as", "12"	=>	"at", "13"	=>	"au", "14"	=>	"az", "15"	=>	"ba", "16"	=>	"bd", "17"	=> "com.bd", "18"	=>	"be", "19"	=>	"bf",
		"20"	=>	"bg", "21"	=>	"com.bh", "22"	=>	"bi", "23"	=>	"bj", "24"	=>	"com.bn", "25"	=>	"com.bo", "26"	=>	"com.br", "27"	=>	"bs", "28"	=>	"bt", "29"	=>	"co.bw", "30"	=>	"by", "31"	=>	"com.bz", "32"	=>	"ca", "33"	=>	"com.kh", "34"	=>	"cc", "35"	=>	"cd", "36"	=>	"cf", "37"	=>	"cat", "38"	=>	"cg", "39"	=>	"ch", "40"	=>	"ci", "41"	=>	"co.ck", "42"	=>	"com", "43"	=>	"cm", "44"	=>	"cn", "45"	=>	"com.co", "46"	=>	"co.cr", "47"	=>	"com.cu", "48"	=>	"cv", "49"	=>	"com.cv", "50"	=>	"cz", "51"	=>	"de", "52"	=>	"dj", "53"	=>	"dk", "54"	=>	"dm", "55"	=>	"com.do", "56"	=>	"dz", "57"	=>	"ec", "58"	=>	"ee", "59"	=>	"eg", "60"	=>	"es", "61"	=>	"com.et", "62"	=>	"fi", "63"	=>	"fj", "64"	=>	"fm", "65"	=>	"fr", "66"	=>	"ga", "67"	=>	"ge", "68"	=>	"gf", "69"	=>	"gg", "70"	=>	"com.gh", "71"	=>	"com.gi", "72"	=>	"gl", "73"	=>	"gm", "74"	=>	"gp", "75"	=>	"gr", "76"	=>	"com.gt", "77"	=>	"gy", "78"	=>	"com.hk", "79"	=>	"hn", "80"	=>	"hr", "81"	=>	"ht", "82"	=>	"hu", "83"	=>	"co.id", "84"	=>	"ir", "85"	=>	"iq", "86"	=>	"ie", "87"	=>	"co.il", "88"	=>	"im", "89"	=>	"co.in", "90"	=>	"io", "91"	=>	"is", "92"	=>	"it", "93"	=>	"je", "94"	=>	"com.jm", "95"	=>	"jo", "96"	=>	"co.jp", "97"	=>	"co.ke", "98"	=>	"ki", "99"	=>	"kg", "100"	=>	"co.kr", "101"	=>	"com.kw", "102"	=>	"kz", "103"	=>	"la", "104"	=>	"com.lb", "105"	=>	"com.lc", "106"	=>	"li", "107"	=>	"lk", "108"	=>	"co.ls", "109"	=>	"lt", "110"	=>	"lu", "111"	=>	"lv", "112"	=>	"com.ly", "113"	=>	"co.ma", "114"	=>	"md", "115"	=>	"me", "116"	=>	"mg", "117"	=>	"mk", "118"	=>	"ml", "119"	=>	"com.mm", "120"	=>	"mn", "121"	=>	"ms", "122"	=>	"com.mt", "123"	=>	"mu", "124"	=>	"mv", "125"	=>	"mw", "126"	=>	"com.mx", "127"	=>	"com.my", "128"	=>	"co.mz", "129"	=>	"com.na", "130"	=>	"ne", "131"	=>	"com.nf", "132"	=>	"com.ng", "133"	=>	"com.ni", "134"	=>	"nl", "135"	=>	"no",	));
	}
	$sql_country = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key LIKE 'arc_countries'");
	$countries = unserialize($sql_country[0]->meta_value);

	return $countries;  
    }

    
    public function rankchecker_checkbox_field_0_render(  ) { 

	?>
	<input type='name' name='rankchecker_settings[rankchecker_checkbox_field_0]' value="<?php echo site_url(); ?>" disabled style="width:300px;">
	<?php

    }

    public function rankchecker_checkbox_field_1_render(  ) { 

	$options = get_option( 'rankchecker_settings' );
	?>
	<input type='checkbox' name='rankchecker_settings[rankchecker_checkbox_field_1]' <?php checked( $options['rankchecker_checkbox_field_1'], 1 ); ?> value='1'>
	<?php

    }
    
    public function rankchecker_select_field_0_render(  ) { 
	$countries = $this->countries();
	$options = get_option( 'rankchecker_settings' );
	?>
	<select name='rankchecker_settings[rankchecker_select_field_0]' class="country" onchange="select_list();">
	<?php
            $count = 0;
            foreach($countries as $country) { 
            ?>
                <option value="<?php echo $count; ?>" <?php selected( $options['rankchecker_select_field_0'], $count);?>><?php echo 'Google ('. strtoupper($country).')'; ?></option>
            <?php	
            $count++;
            }
	?>
	</select>
	<?php
		
		if(empty($options['rankchecker_select_field_0'])) {
			echo 'test';
		}

    }
    
    public function add_country() {
        global $wpdb;
        ?>
        <div class="add_country">
             <form action="" method="POST">
            <input type="text" name="country" placeholder="extension example (NL)">
            <input type="submit" name="add_country" class="btn btn-sm btn-primary add_country_to_list" value="Add country">
            </form>
            <p>If you dont know your country extension, please go to <a href="http://en.wikipedia.org/wiki/List_of_Google_domains" target="_blank">http://en.wikipedia.org/wiki/List_of_Google_domains</a><br>and look at the column Domain</p>
        </div>
        <?php
        $sql_country = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key LIKE 'arc_countries'");
	$countries = unserialize($sql_country[0]->meta_value);
        if(!empty($_POST['country'])) {
            array_push($countries, $_POST['country']);
            update_post_meta('8888', 'arc_countries', $countries);
            ?>
            <script type="text/javascript">
                location.reload();
            </script>
            <?php
        }
        
    }

    public function rankchecker_settings_section_callback(  ) { 

	echo __( 'Options page Rank Checker', 'wordpress' );

    }


    public function rankchecker_options_page(  ) { 
        global $wpdb;
	?>
	<form action='options.php' method='post'>
		
		<h2>Advanced Rank Checker</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
        
        <?php
        
        $this->add_country();


    }
    
}

$rankchecker_options = new rankchecker_options();