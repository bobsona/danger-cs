<?php
/**
*
* @package phpBB SEO GYM Sitemaps
* @version $Id: gym_sitemaps.php 331 2011-11-11 15:42:06Z dcz $
* @copyright (c) 2006 - 2010 www.phpbb-seo.com
* @license http://opensource.org/osi3.0/licenses/lgpl-license.php GNU Lesser General Public License
*
*/
// First basic security
if ( !defined('IN_PHPBB') ) {
	exit;
}
require($phpbb_root_path . 'gym_sitemaps/includes/gym_common.' . $phpEx);
// For Compatibility with the phpBB SEO mod rewrites
if (empty($phpbb_seo)) {
	if (!class_exists('phpbb_seo'/*, false*/)) {
		require($phpbb_root_path . 'gym_sitemaps/includes/phpbb_seo_class_light.' . $phpEx);
	}
	$phpbb_seo = new phpbb_seo();
	define('STARTED_LIGHT', true);
}
/**
* gym_sitemaps Class
* www.phpBB-SEO.com
* @package phpBB SEO
*/
class gym_sitemaps {
	// $_GET vars
	var $actions = array();
	// Working vars
	var $gym_config = array();
	var $override = array();
	var $override_type = array();
	var $gym_output;
	var $output_data = array();
	var $cache_config = array();
	var $yahoo_config = array();
	var $ext_config = array();
	var $gzip_config = array();
	var $style_config = array();
	var $gym_auth = array();
	var $module_auth = array();
	/**
	* constuctor
	*/
	function gym_sitemaps($action_type = '') {
		global $phpEx, $phpbb_seo, $user, $config, $phpbb_root_path, $_action_types, $_override_types, $auth;
		$start_time = $phpbb_seo->microtime_float();
		// Set default values
		$this->gym_config = $this->actions = array();
		$this->override_type = $_override_types;
		$this->actions['action_types'] = $_action_types;
		$this->actions['action_type'] = in_array($action_type, $this->actions['action_types']) ? $action_type : '';
		$this->actions['extra_params'] = $this->actions['extra_params_full'] = $this->actions['auth_param'] = $this->actions['sql_report_msg'] = '';
		$this->actions['auth_guest_list'] = $this->actions['auth_guest_read'] = $this->actions['auth_view_list'] = $this->actions['auth_read_list'] = array();
		$this->actions['robots_patterns'] = array();
		if (empty($this->actions['action_type']) ) {
			$this->gym_error(403, '', __FILE__, __LINE__);
		}
		// Grab required config
		obtain_gym_config($this->actions['action_type'], $this->gym_config);
		if (empty($this->gym_config) ) {
			$this->gym_error(404, '', __FILE__, __LINE__);
		}
		// Set the overidding options
		$this->set_override();
		$this->path_config = array('gym_path' => $phpbb_root_path . 'gym_sitemaps/',
			'gym_img_url' => $phpbb_seo->seo_path['phpbb_url'] . 'gym_sitemaps/images/');
		// The main array
		$this->output_data = array('microtime'=> $start_time,
			'time'	=> time(),
			'mem_usage' => 0,
			'gen_data' => '',
			'gen_out' => '',
			'url_sofar' => 0,
			'url_sofar_total' => 0,
			'showstats' => 0,
			'data' => '',
			'expires_time' => 0,
		);
		// Ceck the day interval and reset pinged
		if (@$config['gym_today'] < $this->output_data['time']) {
			set_config('gym_today', $this->output_data['time'] + 3600*24, 1);
			set_config('gym_pinged_today', 0, 1);
		}
		$this->url_config = array( 'start_default' => '&amp;start=',
			'google_default' => "sitemap.$phpEx",
			'html_default' => "map.$phpEx",
			'rss_default' => "gymrss.$phpEx",
			'gzip_ext_out' => '',
			'zero_dupe' => (boolean) $this->gym_config['gym_zero_dupe'],
			'uri' => $phpbb_seo->seo_path['uri'],
			'current' => '',
			'modrewrite' => false,
		);
		$this->gzip_config = array('gzip_level' => (int) $this->gym_config['gym_gzip_level']);
		$this->cache_config = array(
			'do_cache' => true, // this is used when preventing the caching of private content.
			'cached' => 'false',
			'mod_since' => (boolean) $this->gym_config['gym_mod_since'],
		);
		// init $gym_auth
		$this->gym_auth = array(
			'admin' => $auth->acl_gets('a_') ? true : false,
			'globalmod' => $auth->acl_getf_global('m_') ? true : false,
			'reg' => $user->data['is_registered'] ? true : false,
			'guest' => $user->data['is_registered'] ? false : true,
			'all' => true,
			'none' => false,
		);
		// Workaround for error message handling
		$phpbb_seo->file_hbase['map'] = $phpbb_seo->file_hbase['gymrss'] = $phpbb_seo->file_hbase['sitemap'] = $phpbb_seo->seo_path['phpbb_url'];
		// Clear buffer, just in case it was started elswhere
		while (@ob_end_clean());
		return;
	}
	/**
	* init_get_vars ().
	* Get and check the basic get vars
	* @access private
	*/
	function init_get_vars() {
		// Builds the action_modules array
		$this->init_action_modules();
		// Basic options : gymfile.php?var(=value)
		$this->actions['module_main'] = $this->actions['module_sub'] = '';
		foreach ($this->actions['action_modules'] as $module) {
			if (isset($_GET[$module])) {
				$this->actions['module_main'] = $module;
				$this->actions['module_sub'] = !empty($_GET[$module]) ? trim(utf8_htmlspecialchars(str_replace(array("\n", "\r", "\0"), '', $_GET[$module]))) : '';
				unset($_GET[$module]);
			}
		}
		return;
	}
	/**
	* init_action_modules ().
	* Build the autogenerated array of all expected actions
	* @access private
	*/
	function init_action_modules() {
		global $cache, $phpEx;
		if (($this->actions['action_modules'] = $cache->get('_gym_action_' . $this->actions['action_type'])) === false) {
			$this->actions['action_modules'] = array();
			$dir = @opendir( $this->path_config['gym_path'] . 'modules' );
			$action_from_file = '';
			while( ($file = @readdir($dir)) !== FALSE ) {
				if(preg_match('`^' . $this->actions['action_type'] . '_[a-z0-9_-]+\.' . $phpEx . '$`i', $file)) {
					$action_from_file = trim(str_replace( $this->actions['action_type'] . '_', '' , str_replace('.' . $phpEx , '' ,$file)), "/");
					if (@$this->gym_config[$this->actions['action_type'] . '_' . $action_from_file . '_installed']) {
						$this->actions['action_modules'][$action_from_file] = $action_from_file;
					}
				}
			}
			@closedir($dir);
			$cache->put('_gym_action_' . $this->actions['action_type'], $this->actions['action_modules']);
		}
	}
	/**
	* load_modules ( $module_type, $method = '' ).
	* loads all modules for a given action_type
	* Optional, starts a method
	* @access private
	*/
	function load_modules( $method = ''  ) {
		foreach ( $this->actions['action_modules'] as $module ) {
			$this->load_module( $this->actions['action_type'] . "_$module", $method);
		}
	}
	/**
	* load_module ( $module_class, $method = '' ).
	* loads a module for a given action
	* Optional, starts a method
	* @access private
	*/
	function load_module( $module_class, $method = '', $return = false ) {
		global $phpEx;
		$module_file = $this->path_config['gym_path'] . 'modules/' . $module_class . '.' . $phpEx;
		if ( !empty($this->gym_config[$module_class . '_installed']) && file_exists($module_file) ) {
			if (!class_exists($module_class/*, false*/)) {
				require($module_file);
			}
			if (class_exists($module_class/*, false*/)) {
				$gym_module = new $module_class($this);
				if ( !empty($method) && method_exists($gym_module, $method)) {
					$gym_module->$method();
				}
				if ($return) {
					return $gym_module;
				}
			}
		} else {
			$this->gym_error(500, '', __FILE__, __LINE__);
		}
	}
	/**
	* gym_init_output ()
	* In case we need to hanlde the output
	* @access private
	*/
	function gym_init_output() {
		global $phpEx;
		if (!class_exists('gym_output'/*, false*/)) {
			require($this->path_config['gym_path'] . 'includes/gym_output.' . $phpEx);
		}
		$this->gym_output = new gym_output($this);
	}
	/**
	* gym_auth_value()
	* @access private
	*/
	function gym_auth_value($value) {
		return !empty($this->gym_auth[$value]);
	}
	/**
	* set_module_option($config_key, $override ='')
	* will check if a module config value is available
	* Set it or use the main type default value or the main global value
	* Globale module value is used when cyclying through modules ( $this->actions['module_main'] = '' )
	* $override = global => global config
	* $override = output_type => module config
	* $override = module => sub module config
	* degrades to the global config in case there is no better otpion
	* @access private
	*/
	function set_module_option($config_key, $override = OVERRIDE_MODULE) {
		$cond = '';
		// Check if we have a sub module option
		if ( ($override == OVERRIDE_MODULE) && @isset($this->gym_config[$this->actions['action_type'] . '_' . $this->actions['module_main'] . "_$config_key"]) ) {
			return $this->gym_config[$this->actions['action_type'] . '_' . $this->actions['module_main'] . "_$config_key"];
		}
		// Else look for an output type option
		if ( ($override != OVERRIDE_GLOBAL) && @isset($this->gym_config[$this->actions['action_type'] . "_$config_key"]) ) {
			return $this->gym_config[$this->actions['action_type'] . "_$config_key"];

		}
		// Else return the global config value or the next available valid option from the output type to the module level or null
		return isset($this->gym_config["gym_$config_key"]) ? $this->gym_config["gym_$config_key"] : ( @isset($this->gym_config[$this->actions['action_type'] . "_$config_key"]) ? $this->gym_config[$this->actions['action_type'] . "_$config_key"] : ( @isset($this->gym_config[$this->actions['action_type'] . '_' . $this->actions['module_main'] . "_$config_key"]) ? $this->gym_config[$this->actions['action_type'] . '_' . $this->actions['module_main'] . "_$config_key"] : null) );
	}
	/**
	* set_override()
	* Will set the three levels of overriding
	*/
	function set_override() {
		foreach ($this->override_type as $type) {
			$this->override[$type] = $this->_set_override($type);
		}
		$this->override[$this->actions['action_type']] = $this->gym_config[$this->actions['action_type'] . '_override'];
		return;
	}
	/**
	* _set_override()
	* helper for set_override()
	*/
	function _set_override($type) {
		$main_key = 'gym_override_' . $type;
		$mode_key = $this->actions['action_type'] . '_override_' . $type;
	//	$module_key = !empty($this->actions['module_main']) ? $this->actions['action_type'] . '_' . $this->actions['module_main'] . '_override_' . $type : 0;
		if ($this->gym_config['gym_override']) { // if top level overrinding is activated
			return ($this->gym_config[$main_key] != OVERRIDE_GLOBAL)  ? ($this->gym_config[$mode_key] != OVERRIDE_GLOBAL ? $this->gym_config[$mode_key] : $this->gym_config[$main_key]) : OVERRIDE_GLOBAL;
		} else {
			return $this->gym_config[$mode_key];
		}
	}
	/**
	 * xml_encode()
	 * helper
	 */
	function xml_encode($utf8_string) {
		static $find = array('&', '<', '>');
		static $replace = array('&#x26;', '&#x3C;', '&#x3E;');
		return numeric_entify_utf8(str_replace($find, $replace, $utf8_string));
	}
	/**
	* check_forum_auth()
	* Returns various forum auth and properties
	*/
	function check_forum_auth($guest_auth = true) {
		global $auth, $db, $user, $cache;
		$forum_auth_list = array('list' => array(), 'read' => array(), 'list_post' => array(), 'read_post' => array(), 'public_list' => array(), 'public_read' => array(), 'skip_pass' => array(), 'skip_cat' => array(), 'skip_all' => array(), 'skip_link' => array());
		$need_cache = false;
		$cache_file = '_gym_auth_forum_guest';
		// First check the public forum list
		if (($forum_auth_list = $cache->get($cache_file)) === false) {
			$forum_auth_list = array('list' => array(), 'read' => array(), 'list_post' => array(), 'read_post' => array(), 'public_list' => array(), 'public_read' => array(), 'skip_pass' => array(), 'skip_cat' => array(), 'skip_all' => array(), 'skip_link' => array());
			$guest_data = array('user_id' => ANONYMOUS,
				'user_type' => USER_IGNORE,
				'user_permissions' . (defined('XLANG_AKEY') ? XLANG_AKEY : '') => '',
			);
			$g_auth = new auth();
			$g_auth->acl($guest_data);
			// the forum id array
			$forum_list_ary = $g_auth->acl_getf('f_list', true);
			foreach ($forum_list_ary as $forum_id => $null) {
				$forum_auth_list['list'][$forum_id] = (int) $forum_id;
			}
			$forum_read_ary = $g_auth->acl_getf('f_read', true);
			foreach ($forum_read_ary as $forum_id => $null) {
				$forum_auth_list['read'][$forum_id] = (int) $forum_id;
			}
			ksort($forum_auth_list['list']);
			ksort($forum_auth_list['read']);
			$sql = "SELECT forum_id, forum_type, forum_password
				FROM " . FORUMS_TABLE . "
				WHERE	forum_type <> " . FORUM_POST . " OR forum_password <> ''";
			$result = $db->sql_query($sql);
			while ( $row = $db->sql_fetchrow($result) ) {
				$forum_id = (int) $row['forum_id'];
				if ($row['forum_password']) {
					$forum_auth_list['skip_pass'][$forum_id] = $forum_id;
				}
				if ($row['forum_type'] == FORUM_CAT) {
					$forum_auth_list['skip_cat'][$forum_id] = $forum_id;
				} else if ($row['forum_type'] == FORUM_LINK) {
					$forum_auth_list['skip_link'][$forum_id] = $forum_id;
				}
				$forum_auth_list['skip_all'][$forum_id] = $forum_id;
			}
			$db->sql_freeresult($result);
			ksort($forum_auth_list['skip_pass']);
			ksort($forum_auth_list['skip_all']);
			ksort($forum_auth_list['skip_link']);
			ksort($forum_auth_list['skip_cat']);
			// Never mind about fourm links
			$forum_auth_list['read'] = array_diff_assoc($forum_auth_list['read'], $forum_auth_list['skip_link']);
			$forum_auth_list['list'] = array_diff_assoc($forum_auth_list['list'], $forum_auth_list['skip_link']);
			ksort($forum_auth_list['read']);
			ksort($forum_auth_list['list']);
			$forum_auth_list['list_post'] = array_diff_assoc($forum_auth_list['list'], $forum_auth_list['skip_all']);
			$forum_auth_list['read_post'] = array_diff_assoc($forum_auth_list['read'], $forum_auth_list['skip_all']);
			$forum_auth_list['public_list'] = array_diff_assoc($forum_auth_list['list'], $forum_auth_list['skip_pass']);
			$forum_auth_list['public_read'] = array_diff_assoc($forum_auth_list['read'], $forum_auth_list['skip_pass']);
			$cache->put($cache_file, $forum_auth_list);
		}

		$this->module_auth['forum'] = & $forum_auth_list;
		if ($guest_auth) { // sometime, we need to only check guest auths, even if user is registered
			$this->actions['auth_param'] = implode('-', $forum_auth_list['read_post']);
			return $forum_auth_list['read_post'];
		}
		// else handle the real auth
		$forum_auth_list['read'] = $forum_auth_list['list'] = array();
		$forum_list_ary = $auth->acl_getf('f_list', true);
		foreach ($forum_list_ary as $forum_id => $null) {
			$forum_auth_list['list'][$forum_id] = (int) $forum_id;
		}
		$forum_read_ary = $auth->acl_getf('f_read', true);
		foreach ($forum_read_ary as $forum_id => $null) {
			$forum_auth_list['read'][$forum_id] = (int) $forum_id;
		}
		ksort($forum_auth_list['list']);
		ksort($forum_auth_list['read']);
		$forum_auth_list['list'] = array_diff_assoc($forum_auth_list['list'], $forum_auth_list['skip_link']);
		$forum_auth_list['read'] = array_diff_assoc($forum_auth_list['read'], $forum_auth_list['skip_link']);
		$forum_auth_list['list_post'] = array_diff_assoc($forum_auth_list['list'], $forum_auth_list['skip_all']);
		$forum_auth_list['read_post'] = array_diff_assoc($forum_auth_list['read'], $forum_auth_list['skip_all']);

		$this->actions['auth_param'] = implode('-', $forum_auth_list['read_post']);
		return $forum_auth_list['read_post'];
	}
	/**
	* Smiley processing, the phpBB3 function, but, with absolute linking
	* a little optimization and regular config bypass.
	*/
	function smiley_text($text, $force_option = false) {
		global $config, $user, $phpbb_seo;
		static $viewsmilies;
		if (!isset($viewsmilies)) { // Costs less than optionget ;-)
			$viewsmilies = $user->optionget('viewsmilies');
		}
		if ($force_option || !$viewsmilies) {
			return preg_replace('#<!\-\- s(.*?) \-\-><img src="\{SMILIES_PATH\}\/.*? \/><!\-\- s\1 \-\->#', '\1', $text);
		} else {
			return preg_replace('#<!\-\- s(.*?) \-\-><img src="\{SMILIES_PATH\}\/(.*?) \/><!\-\- s\1 \-\->#', '<img src="' . $phpbb_seo->seo_path['phpbb_url'] . $config['smilies_path'] . '/\2 />', $text);
		}
	}
	/**
	* obtain_robots_disallows()
	* obtain the eventual robots.txt exclusions
	* and parse them into a patern array for later use
	* @access private
	*/
	function obtain_robots_disallows() {
		global $phpbb_root_path, $phpbb_seo, $cache, $phpEx;
		if (function_exists('file_get_contents')) {
			// Build the domain root path
			$phpbb_real_path = trim(phpbb_realpath($phpbb_root_path), '/');
			$root_real_path = str_replace(trim($phpbb_seo->seo_path['phpbb_script'], '/'), '', $phpbb_real_path);
			if (file_exists($root_real_path . '/robots.txt')) {
				$time_created = filemtime($root_real_path . '/robots.txt');
				if (($this->actions['robots_patterns'] = $cache->get('_gym_config_robots_regex' )) === false) {
					$robots = file_get_contents($root_real_path . '/robots.txt');
					preg_match_all('`^Disallow[\s]*:[\s]*([a-z0-9_\.&;\?,:/-]+)[\s]*$`im', $robots, $matches,PREG_SET_ORDER);
					if (!empty($matches[0][1])) {
						foreach ($matches as $match) {
							if (!empty($match[1])) {
								$this->actions['robots_patterns'][] = $phpbb_seo->seo_path['root_url'] . trim($match[1], '/');
							}
						}
					}
					$this->actions['robots_patterns']['date'] = $time_created;
					$cache->put('_gym_config_robots_regex' , $this->actions['robots_patterns']);
				} elseif ($this->actions['robots_patterns']['date'] < $time_created) { // robots.tx was updated
					$cache->destroy('_gym_config_robots_regex');
					$this->obtain_robots_disallows();
				}

			}
		}
		return;
	}
	/**
	* is_robots_disallowed()
	* checks if an url is disallowed by the robots.txt patterns
	* @access private
	*/
	function is_robots_disallowed($url) {
		if (!empty($this->actions['robots_patterns'])) {
			foreach($this->actions['robots_patterns'] as $pattern) {
				if (stripos( $url, $pattern) !== false) {
					return true;
				}
			}
		}
		return false;
	}
	/**
	* set_exclude_list($id_list) will build up the public unauthed ids
	* @access private
	*/
	function set_exclude_list($id_list) {
		$exclude_list = empty($id_list) ? array() : explode(',', $id_list);
		$ret = array();
		foreach ($exclude_list as $value ) {
			$value = (int) trim($value);
			if (!empty($value)) {
				$ret[$value] = $value;
			}
		}
		return $ret;
	}
	/**
	* set_exclude_list($id_list) will build up the public unauthed ids
	* This method is deprecated since 2.0.RC2
	* @access private
	*/
	function set_not_in_list($id_list = array(), $field = '', $and = '') {
		if ( !empty($id_list) && is_array($id_list) ) {
			$not_in_id_sql = " $field NOT IN (" . implode(",", array_map('intval', $id_list)) . ") $and ";
		} else {
			$not_in_id_sql = '';
		}
		return $not_in_id_sql;
	}
	/**
	* is_forum_public($forum_id) Will tell if a forum is publicly viewable or listable (auth guest)
	* @access private
	*/
	function is_forum_public($forum_id, $type = 'read') {
		$type = $type === 'list' ? 'list' : 'read';
		return (boolean) isset($this->actions["auth_guest_$type"][$forum_id]);
	}

	// --> Others <--
	/**
	* init_url_rewrite()
	*/
	function init_url_rewrite($modr = false, $type = 0) {
		global $phpbb_seo, $phpEx;
		$this->url_config['modrewrite'] = $modr ? true : false;
		// Mod rewrite type auto detection
		$this->url_config['modrtype'] = !empty($phpbb_seo->modrtype) ? max(0, (int) $phpbb_seo->modrtype) : max(0, (int) $type);
		if (!@isset($phpbb_seo->seo_opt['url_rewrite'])) {
			$phpbb_seo->seo_opt['url_rewrite'] = $this->url_config['modrtype'] > 0 ? true : false;
		}
		// make sure virtual_folder uses the proper value
		$phpbb_seo->seo_opt['virtual_folder'] = $this->url_config['modrtype'] ? $phpbb_seo->seo_opt['virtual_folder'] : false;
		$this->url_config['forum_start_tpl'] = $this->url_config['start_default'] . '%1$d';
		$this->url_config['topic_start_tpl'] = $this->url_config['start_default'] . '%1$d';
		$this->url_config['forum_tpl'] = "viewforum.$phpEx?f=%1\$d";
		$this->url_config['topic_tpl'] = "viewtopic.$phpEx?f=%1\$d&amp;t=%2\$d";
		if (!$phpbb_seo->seo_opt['url_rewrite']) {
			$this->url_config['forum_index'] = "index.$phpEx";
			$phpbb_seo->seo_opt['virtual_folder'] = false;
			$this->url_config['forum_ext'] = '';
			$this->url_config['topic_ext'] = '';
		} else {
			$this->url_config['forum_index'] = !empty($phpbb_seo->seo_static['index']) ? $phpbb_seo->seo_static['index'] . $phpbb_seo->seo_ext['index'] : '';
			if ($this->url_config['modrtype'] >= 1) { // Simple mod rewrite, default is none (0)
				$this->url_config['forum_ext'] = $phpbb_seo->seo_ext['forum'];
				$this->url_config['topic_ext'] = $phpbb_seo->seo_ext['topic'];
				$this->url_config['forum_start_tpl'] = $phpbb_seo->seo_opt['virtual_folder'] ? '/' . $phpbb_seo->seo_static['pagination'] . '%1$d' . $phpbb_seo->seo_ext['pagination'] : $phpbb_seo->seo_delim['start'] . '%1$d' . $this->url_config['forum_ext'];
				$this->url_config['topic_start_tpl'] = $this->url_config['topic_ext'] == '/' ? '/' . $phpbb_seo->seo_static['pagination'] . '%1$d' . $phpbb_seo->seo_ext['pagination'] : $phpbb_seo->seo_delim['start'] . '%1$d' . $this->url_config['topic_ext'];
			}
			if ($this->url_config['modrtype'] >= 2) { // +Mixed
			}
			if ($this->url_config['modrtype'] >= 3) { // +Advanced
			}
		}
	}
	/**
	* forum_url() builds forum url with proper options
	* Suffixe is not added here, to properly deal with pagination
	*/
	function forum_url($forum_name, $forum_id) {
		global $phpbb_seo;
		return $phpbb_seo->seo_opt['url_rewrite'] ? $phpbb_seo->set_url($forum_name, $forum_id, 'forum') : sprintf($this->url_config['forum_tpl'], $forum_id);
	}
	/**
	* topic_url($topic_title, $topic_id, $forum_url, $forum_id) builds forum url with proper options
	* Suffixe is not added here, to properly deal with pagination
	*/
	function topic_url($topic_data, $forum_id, $forum_url = '') {
		global $phpbb_seo;
		return $phpbb_seo->seo_opt['url_rewrite'] ? $phpbb_seo->prepare_iurl($topic_data, 'topic', trim($forum_url, '/')) : sprintf($this->url_config['topic_tpl'], $forum_id, (int) $topic_data['topic_id']);
	}
	/**
	* Returns usable start param
	* -xx | /pagexx.html
	*/
	function set_start($type, $start) {
		global $phpbb_seo;
		return $start > 0 ? sprintf($this->url_config[$type . '_start_tpl'], (int) $start) : $this->url_config[$type . '_ext'];
	}
	/**
	* check start var consistency
	* and return our best guess for $start, eg the first valid page
	* parameter according to pagination settings being lower
	* than the one sent.
	*/
	function chk_start($start = 0, $limit = 0) {
		if ($limit > 0) {
			$start = is_int($start/$limit) ? $start : intval($start/$limit)*$limit;
		}
		return (int) $start;
	}
	/**
	* parse_link() builds an html link
	*/
	function parse_link($url, $title = '', $tag_arround = '') {
		global $config;
		static $linktpl = '%4$s<a href="%1$s" %2$s>%3$s</a>%5$s';
		$title_tag = $tag1 = $tag2 = '';
		if (empty($title) ) {
			$title = $url;
		} else {
			$title_tag = 'title="' . $title . '"';
		}
		if (!empty($tag_arround)) {
			$tag1 = "<$tag_arround>";
			$tag2 = "</$tag_arround>";
		}
		return sprintf($linktpl, $url, $title_tag, $title, $tag1, $tag2);
	}
	/**
	* seo_kill_dupes($url) will kill duplicates when pages are not cached
	* @access private
	*/
	function seo_kill_dupes($url) {
		global $user, $auth, $_SID, $phpbb_seo;
		$url = str_replace('&amp;', '&', $url);
		// if an https request lead us here or if it is forced, then use it as a reference
		$url = $phpbb_seo->sslify($url, $phpbb_seo->ssl['use']);
		if ($this->url_config['zero_dupe']) {
			$requested_url = $this->url_config['uri'];
			if (!empty($_REQUEST['explain']) && (boolean) ($auth->acl_get('a_') && defined('DEBUG_EXTRA'))) {
				if ($_REQUEST['explain'] == 1) {
					return;
				}
			}
			$url = $phpbb_seo->drop_sid($url);
			if (!empty($_GET['sid']) && !empty($_SID)) {
				$url .=  (utf8_strpos( $url, '?' ) !== false ? '&' : '?') . 'sid=' . $user->session_id;
			}
			if ( $requested_url !== $url ) {
				$this->gym_redirect($url);
			}
		}
		$this->url_config['current'] = $url;
		return;
	}
	/**
	* Custom HTTP 301 redirections.
	* To kill duplicates
	*/
	function gym_redirect($url, $header = "301 Moved Permanently", $code = 301, $replace = true) {
		if (headers_sent()) {
			return false;
		}
		if (strstr(urldecode($url), "\n") || strstr(urldecode($url), "\r") || strstr(urldecode($url), ';url')) {
			$this->gym_error(400, '',  __FILE__, __LINE__);
		}
		$http = "HTTP/1.1 ";
		header($http . $header, $replace, $code);
		header("Location:" . $url);
		$this->safe_exit();
	}
	/**
	* gym_error(($errno, $msg_text, $errfile, $errline)
	* Will properly handle error for all cases, admin always get full debug
	* Partly based on msg_handler()
	* @access private
	*/
	function gym_error($errno = 0, $msg_key = '', $errfile = '', $errline = '', $sql = '') {
		global $user, $phpbb_seo, $auth, $phpbb_root_path, $phpEx, $msg_title;
		$http_codes = array (
			204 => 'HTTP/1.1 204 No Content',
			400 => 'HTTP/1.1 400 Bad Request',
			401 => 'HTTP/1.1 401 Unauthorized',
			403 => 'HTTP/1.1 403 Forbidden',
			404 => 'HTTP/1.1 404 Not Found',
			405 => 'HTTP/1.1 405 Method Not Allowed',
			406 => 'HTTP/1.1 406 Not Acceptable',
			410 => 'HTTP/1.1 410 Gone',
			500 => 'HTTP/1.1 500 Internal Server Error',
			503 => 'HTTP/1.1 503 Service Unavailable',
		);
		$header = isset($http_codes[$errno]) ? $http_codes[$errno] : '';
		$return_url = append_sid("{$phpbb_root_path}index.$phpEx");
		if (!empty($user) && !empty($user->lang)) {
			$msg_title = (empty($msg_key)) ? ( !empty($user->lang['GYM_ERROR_' . $errno]) ? $user->lang['GYM_ERROR_' . $errno] :  ( !empty($header) ? $header : $user->lang['GENERAL_ERROR'])  ) : ((!empty($user->lang[$msg_key])) ? $user->lang[$msg_key] : $msg_key);
			$msg_text = !empty($user->lang[$msg_key . '_EXPLAIN']) ? $user->lang[$msg_key . '_EXPLAIN'] : (!empty($user->lang['GYM_ERROR_' . $errno . '_EXPLAIN']) ?  $user->lang['GYM_ERROR_' . $errno . '_EXPLAIN'] : ( (!empty($msg_key) ? $msg_key : (!empty($header) ? $header : $msg_title) ) ) );
			$l_return_index = sprintf($user->lang['RETURN_INDEX'], '<a href="' . $return_url . '">', '</a>');
			if ( ( $errno == 500 || $errno == 503 ) && !empty($config['board_contact'])) {
				$msg_text .= '<p>' . sprintf($user->lang['NOTIFY_ADMIN_EMAIL'], $config['board_contact']) . '</p>';
			}
		} else {
			$msg_title = 'GYM Sitemaps General Error';
			$l_return_index = '<a href="' . $return_url . '">Return to index page</a>';
			if ( ( $errno == 500 || $errno == 503 ) && !empty($config['board_contact'])) {
				$msg_text .= '<p>Please notify the board administrator or webmaster: <a href="mailto:' . $config['board_contact'] . '">' . $config['board_contact'] . '</a></p>';
			}
		}
		$msg_text .= '<br/><br/>' . $l_return_index;
		if (@$auth->acl_get('a_')) {
			if (!empty($user->lang[$msg_key . '_EXPLAIN_ADMIN'])) {
				$msg_text .= '<br/><br/>' . $user->lang[$msg_key . '_EXPLAIN_ADMIN'];
			}
			if (defined('DEBUG')) {
				$msg_text .= '</p><br/><h2>Debug :</h2><p>' . (!empty($errfile) ? "<br/><b>File :</b> " . utf8_htmlspecialchars($errfile) . "<br/>" : '');
				$msg_text .= !empty($errline) ? "<br/><b>Line :</b> " . utf8_htmlspecialchars($errline) . "<br/>" : '';
				$msg_text .= !empty($sql) ? "<br/><b>Sql :</b>  " . utf8_htmlspecialchars($sql) . "<br/>" : '';
				$msg_text .= '</p><div style="font-size:12px">' . get_backtrace() . '</div><p>';
			}
		}
		if ( !empty($header) ) {
			header($header);
		}
		meta_refresh(5, $return_url);
		trigger_error($msg_text);
		$this->safe_exit();
		return;
	}
	/**
	* For a safe exit
	* @access private
	*/
	function safe_exit() {
		garbage_collection();
		exit_handler();
		exit;
	}
}
?>
