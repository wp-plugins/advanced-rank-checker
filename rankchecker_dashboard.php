<?php

class rankchecker_dashboard {

    /*
     * Add constructor
     */
    public function __construct() {
        add_action( 'wp_dashboard_setup', array($this, 'add_dashboard_widgets' ));
        add_action( 'admin_head-index.php', function()
			{
			    ?>
			<style>
			.postbox-container {
			    min-width: 100% !important;
			}
			.meta-box-sortables.ui-sortable.empty-container { 
			    display: none;
			}
			</style>
			    <?php
			});
    }
    
    /*
     * Add dashboard widgets
     */
    public function add_dashboard_widgets() {
        wp_add_dashboard_widget( 'show_dashboard', 'SEO Checker', array($this, 'show_dashboard'), '', 'high');
    }
    
    /*
     * Show dashboard info
     */
    public function show_dashboard() {
        $rankchecker = new rankchecker();
        
        $rankchecker->show_ranking($hidecheck = true);
    }    
    
}

$rankchecker_dashboard = new rankchecker_dashboard();
