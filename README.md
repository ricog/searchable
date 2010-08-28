# Searchable Plugin

Copyright 2010 DualTech Services, Inc.

A simple search plugin that allows searching across joined tables. The filterConditions method will return an array of conditions for use in pagination or find methods.

## Example Usage

	class Article extends AppModel {
		...

		var $actsAs = array(
			'Searchable.Searchable' => array(
				'searchType' => 'phrase',
				'searchFields' => array(
					'Article.title',
					'Article.body',
					'Author.name',
				),
			),
		);

		$belongsTo = array(
			'Author',
		);

		...
	}

	class ArticlesController extends AppController {
		...

		function index($searchString) {
			$conditions = array();
			...

			$conditions[] = $this->Article->filterConditions($searchString);
			$articles = $this->Article->find('all', array('conditions' => $conditions));
			...

		}
	}

## Configuration

Configuration is added as an array in the $actsAs attribute of the primary model. See the example above for specific usage.

- SearchFields should be entered as Model.field.
- Possible searchTypes are "exact", "phrase", and "partial".

## Notes

This plugin currently works with joined models only. This means if you search across models (tables) that the associated models must be bound to the parent model using belongsTo. There may be other cases where it works, but I haven't tested them.

This plugin is intended to be nice and simple. CakeDC has a Search plugin availabe that appears to do a lot more. I haven't tried it, but it may be a better fit for your project. [http://github.com/CakeDC/search](http://github.com/CakeDC/search)
