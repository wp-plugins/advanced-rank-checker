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

    
    public function rankchecker_checkbox_field_0_render(  ) { 

	?>
	<input type='name' name='rankchecker_settings[rankchecker_checkbox_field_0]' value="<?php echo site_url(); ?>" disabled style="width:300px;">
	<?php

    }

    public function rankchecker_checkbox_field_1_render(  ) { 

	$options = get_option( 'rankchecker_settings' );
	?>
	<input type='checkbox' name='rankchecker_settings[rankchecker_checkbox_field_1]' <?php checked( $options['rankchecker_checkbox_field_1'], 1 ); ?> value='0'>
	<?php

    }
    
    public function rankchecker_select_field_0_render(  ) { 

	$options = get_option( 'rankchecker_settings' );
	?>
	<select name='rankchecker_settings[rankchecker_select_field_0]'>
	<option value="0" <?php selected( $options['rankchecker_select_field_0'], 0);?>>Select country</option>
	<option value="1" <?php selected( $options['rankchecker_select_field_0'], 1);?>>Google (NL)</option>
	<option value="2" <?php selected( $options['rankchecker_select_field_0'], 2);?>>Google (EN)</option>
	<option value="3" <?php selected( $options['rankchecker_select_field_0'], 3);?>>Google (DE)</option>
	<option value="4" <?php selected( $options['rankchecker_select_field_0'], 4);?>>Google (FR)</option>
	<option value="5" <?php selected( $options['rankchecker_select_field_0'], 5);?>>Google (IT)</option>
	<option value="6" <?php selected( $options['rankchecker_select_field_0'], 6);?>>Google (ES)</option>
	<option value="7" <?php selected( $options['rankchecker_select_field_0'], 7);?>>Google (TW)</option>
	<option value="8" <?php selected( $options['rankchecker_select_field_0'], 8);?>>Google (CA)</option>
	<option value="9" <?php selected( $options['rankchecker_select_field_0'], 9);?>>Google (CN)</option>
	</select>
	<?php

    }

    public function rankchecker_settings_section_callback(  ) { 

	echo __( 'Options page Rank Checker', 'wordpress' );

    }


    public function rankchecker_options_page(  ) { 

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
    }
    
}

$rankchecker_options = new rankchecker_options();