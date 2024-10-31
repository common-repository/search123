<?php
error_reporting(1);
class s123 {

	function s123 ( $args=array() ) {

		$this->kit_version = '5.2';

		$this->allowed_args = array(
            "aid",        // your traffic partner user ID
            "start",      // the number of the search result to return first (used in paging)
            "size",       // the maximum number of results to return
		);

		$this->check_constructor($args);

		$this->defaults($args, "search_url", "http://cgi.search123.uk.com");
		$this->defaults($args, "upstream_ref", urlencode( $this->get_var('HTTP_REFERER') ));
		$this->defaults($args, "user_agent", urlencode($this->get_var('HTTP_USER_AGENT') ));
		$this->defaults($args, "referer", urlencode('http://'. $this->get_var('SERVER_NAME'). $this->get_var('REQUEST_URI')));

		$this->defaults($args, "ip", $this->get_var('REMOTE_ADDR') );
		$this->defaults($args, "x_host", $this->get_var('HTTP_X_FORWARDED_FOR') );
		$this->defaults($args, "start", 0 );
		$this->defaults($args, "size", 10 );
		$this->defaults($args, "aid", 0 );

		$this->type = "Q";

		if ( empty($args[aid]) ) {
			$this->fatal_error("No AID provided to s123() constructor");
		}

		$this->check_session();

	}

	function defaults ($args, $key, $value) {
		empty($args[$key])  ? $this->$key = $value
		: $this->$key = $args[$key]
		;
	}

	function search ($query) {
		if (!$query) { return; }
		if (!$this->aid) { return; }

		$this->enc_query = urlencode($query);
		$this->enc_user_agent= urlencode($this->user_agent);
		$this->listings = array();
		$this->meta = array();
		$this->query = urlencode(urldecode($query));

		$this->base_url .= $this->search_url ."/cgi-bin/XMLFeed.cgi?"
		."aid=$this->aid"
		."&type=2"
		."&uid=1"
		."&ip=$this->ip"
		."&x_host=$this->x_host"
		."&type=$this->type"
		."&size=$this->size"
		."&start=$this->start"
		."&client_ua=$this->enc_user_agent"
		."&client_ref=$this->referer"
		."&upstream_ref=$this->upstream_ref"
		."&usid=$this->session_id"
		."&kv=$this->kit_version"
		."&kt=php"
		."&query=$this->enc_query"
		;

		$this->xml_response = @join ("\n", @file($this->base_url));

		$i = 0;
		foreach ( explode("</LISTING>", $this->xml_response ) as $line) {

			$this->listings[$i][redirect_url] = $this->parse_el("redirect_url", $line);
			$this->listings[$i][site_url]
			= $this->parse_el("site_url", $line);
			$this->listings[$i][title]
			= $this->parse_el("title", $line);
			$this->listings[$i][description]
			= $this->parse_el("description", $line);
			$this->listings[$i][bid]
			= $this->parse_el("bid", $line);
			$this->listings[$i][is_adult]
			= $this->parse_el("is_adult", $line);
			$i++;
		}
		array_pop($this->listings);

		if (!$this->meta[tot_count]) {
			$this->meta[tot_count] = $i-1;
		}

	}

	function parse_attr($el, $attr, $str) {
		if ( preg_match("/<$el\s+$attr\s?=\s?['\"]?([^'\"\s]+)['\"\s]?>/i", $str, $match) ) {
			return trim($match[1]);
		}
	}
	function parse_el($el, $str) {
		if ( preg_match("/<$el>(.*)<\/$el>/i", $str, $match) ) {
			$match[1] = str_replace("<![CDATA[", "", $match[1]);
			$match[1] = str_replace("]]>", "", $match[1]);
			return trim($match[1]);
		}
	}


	function GetRetCount () { return $this->meta[tot_count]; }
	function GetBid ($i) { return $this->listings[$i][bid];}
	function GetSiteURL($i) { return $this->listings[$i][site_url];}
	function GetRedirectURL($i) { return urlencode($this->listings[$i][redirect_url]); }
	function GetTitle($i) { return $this->listings[$i][title];}
	function GetDescription($i) { return $this->listings[$i][description];}

	function no_results () {
		$nomatch = false;

		if ($this->GetRetCount() == 0 ) {
			$nomatch = true;
		}

		if (! $this->GetRetCount() ) {
			$nomatch = true;
		}
		return $nomatch;
	}

	function fatal_error ($error) {
		die($error);
	}

	function or_equals ($var, $value) {
		if ($var == '' ) {
			return $value;
		}
		return $var;
	}

	function check_session () {

		if ( headers_sent() ) {
			return;
		}

		if(($_COOKIE['s123user'])) {
			$this->session_id = $_COOKIE['s123user'];
		}
		else {
			$this->session_id = md5(
			$this->aid
			. $this->user_agent
			. $this->ip
			. $this->get_var('REQUEST_TIME')
			);
			$this->session_id = $this->session_id . '.' . time();
			setcookie('s123user', $this->session_id, time()+60*30, "/");
		}

		if(isset($_COOKIE['S123UID'])) {
			$this->uid = $_COOKIE['S123UID'];
		}
		else {
			mt_srand((float)microtime()*1000000);
			$this->uid = time().mt_rand( 10, 99);
			setcookie('S123UID', $this->uid, time()+153792000, "/");
		}
	}

	function check_constructor ( $args=array() ) {
		$this->constructor_args = $args;

		while ($a = key ($args) ) {
			if (!in_array($a, $this->allowed_args) ) {
				$this->fatal_error(
                            "$a is not a valid argument to s123() constructor"
				);
			}
			next($args);
		}

		if ( sizeof($args) < 1 ) {
			$this->fatal_error("no parameters passed into the s123() constructor !");
		}
	}

	function get_var ($key) {

		$superglobals = array(
                        "_SERVER",
                        "_COOKIE",
                        "_GET",
                        "_POST",
                        "HTTP_POST_VARS",
                        "HTTP_GET_VARS",
		);

		foreach ($superglobals as $name) {
			eval("\$ar = \$$name;");

			if ( isset($ar[$key] ) ) {
				return $ar[$key];
			}
		}

		return false;
	}
}
?>