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
		$this->version = '1.1.0';
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

        if (Tools::isSubmit('updateBranch')) {
            $branch = pSQL(Tools::getValue('branch'));

            if (!$this->replaceFilesByBranch($branch)) {
                $this->errors[] = $this->l('Error occurred during copying files!');
                return '';
            }

            if (!$this->{'update_branch_'.$branch}()) {
                $this->errors[] = $this->l('Error occurred during update function!');
                return '';
            }

            return $branch.' was updated!';
        }

        $this->context->smarty->assign([
            'branches' => $this->getAllBranches(),
        ]);

        return $this->context->smarty->fetch(__DIR__ . '/views/templates/admin/form.tpl');
    }

    private function getAllBranches() {

        $branches = [];

        if ($h = opendir(__DIR__.'/branches')) {
            while (($entry = readdir($h)) !== false) {

                if ($entry==='.' || $entry==='..') {
                    continue;
                }

                $branches[] =  $entry;
            }
            closedir($h);
        }

        return $branches;
    }

    private function replaceFilesByBranch($branch) {

        $path = __DIR__.'/branches/'.$branch;

        // Construct the iterator
        $it = new RecursiveDirectoryIterator($path);

        // Loop through files
        foreach(new RecursiveIteratorIterator($it) as $file) {
           if ($file->getExtension()) {

               if (strpos($file, 'admin-dev')) {
                   $new_file_path = str_replace($path.'/admin-dev', _PS_ADMIN_DIR_, $file);
               }
               else {
                   $new_file_path = str_replace($path, _PS_CORE_DIR_, $file);
               }

               if (!copy($file, $new_file_path)) {
                   return false;
               }
           }
        }


        return true;
    }

    // Specific update functions
    private function update_branch_multiple_features() {

        // Add feature_product_lang table
        $prefix = _DB_PREFIX_;

        $sql = "
            CREATE TABLE IF NOT EXISTS `{$prefix}feature_product_lang` (
                id_product       int                      not null,
                id_feature_value int                      not null,
                id_lang          int                      not null,
                prefix           varchar(100)  default '' not null,
                suffix           varchar(100)  default '' not null,
                displayable      varchar(1000) default '' not null,
            PRIMARY KEY (`id_product`, `id_feature_value`, `id_lang`)
            ) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET='utf8';
        ";

        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        // Clean positions for all features
        $features = Feature::getFeatures($this->context->language->id);
        $ids_feature = array_column($features, 'id_feature');
        foreach ($ids_feature as $id_feature) {
            FeatureValue::cleanPositions($id_feature);
        }

        return true;
    }

}