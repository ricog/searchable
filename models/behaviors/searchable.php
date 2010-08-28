<?php
/**
 * Searchable behavior
 * 
 * Enables easy multi-model search
 */

class SearchableBehavior extends ModelBehavior {

/**
 * Default settings for this behavior
 *
 * @var array
 * @access protected
 * 
 * searchType - a string determining the type of search performed. Options are:
 *     partial   - will find the search string anywhere in the field
 * 	   phrase    - will match the string as a phrase
 * 	   exact     - will only match if the field contents exactly match the search string 
 */
	protected $_baseConfig = array(
		'searchType' => 'exact',
		'searchFields' => array(),
	);

/**
 * Setup the behavior
 *
 * @param object $Model instance of model
 * @param array $config array of configuration settings.
 * @return void
 * @access public
 */
	function setup(&$Model, $config = array()) {
		if (!is_array($config)) {
			$config = array($config);
		}
		$settings = Set::merge($this->_baseConfig, $config);

		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $settings;
		} else {
			$this->settings[$Model->alias] = Set::merge($this->settings[$Model->alias], (array)$settings);
		}
		
		$this->Model = $Model;
	}
	
/**
 * Filter Conditions method
 * 
 * Returns an array of search conditions based on a search string and searchFields
 */
	function filterConditions(&$Model, $search = null, $searchType = null) {
		if (!$searchType) {
			$searchType = $this->settings[$Model->alias]['searchType'];
		}
		$searchFields = $this->settings[$Model->alias]['searchFields'];

		foreach ($searchFields as $fieldName) {
			if (!strstr($fieldName, '.')) {
				$fieldName = $Model->alias . '.' . $fieldName;
			}
			if ($searchType == 'partial') {
				$conditions[$fieldName . ' LIKE'] = '%' . $search . '%';
			} elseif ($searchType == 'phrase') {
				$conditions[] = array(
					$fieldName . ' LIKE' => '%' . $search . '%',
					$fieldName . ' REGEXP' => '[[:<:]]' . $search . '[[:>:]]',
				);
			} else {
				$conditions[$fieldName] = $search;
			}
		}

		return array('OR' => $conditions);
	}
}
