<?php

/**
 * Copyright (C) 2022 Emanuel Schiendorfer
 *
 * @author    Emanuel Schiendorfer <https://github.com/eschiendorfer>
 * @copyright 2022 Emanuel Schiendorfer
 */

if (!defined('_PS_VERSION_'))
	exit;

class Genzo_Branch_Tester extends Module
{
    public $errors;

	function __construct() {
		$this->name = 'genzo_branch_tester';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'Emanuel Schiendorfer';
		$this->need_instance = 0;

		$this->bootstrap = true;

	 	parent::__construct();

		$this->displayName = $this->l('Genzo Branch Tester');
		$this->description = $this->l('With this module you can test core changes from Emanuel Schiendorfer');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

	}

	public function install() {
		if (!parent::install()) {
            return false;
        }

		return true;
	}

	public function uninstall() {
		if (!parent::uninstall()) {
            return false;
        }

		return true;
	}


	// Backoffice
    public function getContent() {
        return 'Genzo Branch Tester installed';
    }

}