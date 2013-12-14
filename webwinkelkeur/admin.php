<?php

class WebwinkelkeurAdmin {
    public function __construct() {
        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('plugin_action_links', array($this, 'plugin_action_links'), 10, 2);
    }

    public function admin_menu() {
        add_submenu_page('plugins.php', __('Webwinkelkeur'), __('Webwinkelkeur'),
                         'manage_options', 'webwinkelkeur', array($this, 'options_page'));
    }

    public function plugin_action_links($links, $file) {
        if($file == 'webwinkelkeur/webwinkelkeur.php') {
            $links[] = '<a href="admin.php?page=webwinkelkeur">' . __('Settings') . '</a>';
        }
        return $links;
    }

    public function options_page() {
        $errors = array();
        $fields = array(
            'wwk_shop_id',
            'wwk_api_key',
            'sidebar',
            'invite',
            'invite_delay',
        );
        $config = array('invite_delay' => 3);

        foreach($fields as $field_name) {
            $value = get_option('webwinkelkeur_' . $field_name, false);
            if($value !== false)
                $config[$field_name] = (string) $value;
            elseif(!isset($config[$field_name]))
                $config[$field_name] = '';
        }

        if(isset($_POST['webwinkelkeur_wwk_shop_id'])) {
            foreach($fields as $field_name)
                $config[$field_name] = (string) @$_POST['webwinkelkeur_' . $field_name];

            if(empty($config['wwk_shop_id']))
                $errors[] = 'Uw webwinkel ID is verplicht.';
            elseif(!ctype_digit($config['wwk_shop_id']))
                $errors[] = 'Uw webwinkel ID kan alleen cijfers bevatten.';

            if($config['invite'] && !$config['wwk_api_key'])
                $errors[] = 'Om uitnodigingen te versturen is uw API key verplicht.';

            if(!$errors)
                foreach($config as $name => $value)
                    update_option('webwinkelkeur_' . $name, $value);
        }

        foreach($errors as $error)
            echo "<div class=error><p>", $error, "</p></div>";
        
        require dirname(__FILE__) . '/options.php';
    }
}

new WebwinkelkeurAdmin;