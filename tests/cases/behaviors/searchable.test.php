<?php
class Article extends CakeTestModel {
	var $name = 'Article';
	var $useTable = 'articles';
	var $actsAs = array(
		'Searchable.Searchable' => array(
			'searchType' => 'exact',
			'searchFields' => array(
				'Article.title',
				'Article.body',
			),
		),
	);
}

class ArticleHasMany extends CakeTestModel {
	var $name = 'ArticleHasMany';
	var $useTable = 'articles';
	var $actsAs = array(
		'Searchable.Searchable' => array(
			'searchType' => 'phrase',
			'searchFields' => array(
				'ArticleHasMany.body',
				'Comment.comment',
			),
		),
	);
	
	var $hasMany = array('Comment');
}

class Comment extends CakeTestModel {
	var $name = 'Comment';
	var $useTable = 'comments';
	var $actsAs = array(
/*
		'Searchable.Searchable' => array(
			'searchType' => 'phrase',
			'searchFields' => array(
				'ArticleHasMany.body',
				'Comment.comment',
			),
		),
*/
		'Containable',
	);
	
	var $belongsTo = array('ArticleHasMany' => array('foreignKey' => 'article_id'));
}

class SearchableTestCase extends CakeTestCase {
	var $fixtures = array('plugin.searchable.article', 'plugin.searchable.comment');
	
	function startTest() {
		$this->Article =& ClassRegistry::init('Article');
		$this->ArticleHasMany =& ClassRegistry::init('ArticleHasMany');
	}
	
	function endTest() {
		unset($this->Article);
		unset($this->ArticleHasMany);
		ClassRegistry::flush();
	}

/**
 * Test our test environment
 */
	function testEnvironment() {
		// Execute a standard find to make sure all our test data is there
		$result = $this->Article->find('all');
		$expected = array(
			0 => array(
				'Article' => array(
					'id' => '1',
					'title' => 'Testing an Article',
					'body' => 'This is a test article',
					'published' => '1',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31',
				),
			),
			1 => array(
				'Article' => array(
					'id' => '2',
					'title' => 'Second Article',
					'body' => 'This is a test article',
					'published' => '1',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31',
				),
			),
			2 => array(
				'Article' => array(
					'id' => '3',
					'title' => 'Third Article',
					'body' => 'Testing an Article',
					'published' => '1',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31',
				),
			),
			3 => array(
				'Article' => array(
					'id' => 4,
					'title' => 'Fourth Article',
					'body' => 'A unique string to match',
					'published' => '1',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
				),
			),
			
		);
		$this->assertEqual($expected,$result);

		// Make sure all the comments are here
		$Comment = ClassRegistry::init('Comment');
		$Comment->contain();
		$result = $Comment->find('all');
		unset($Comment);
		$expected = array(
			0 => array(
				'Comment' => array(
					'id' => '1',
					'article_id' => '1',
					'comment' => 'I really like your post, good job.',
					'published' => '1',
				),
			),
			1 => array(
				'Comment' => array(
					'id' => '2',
					'article_id' => '3',
					'comment' => 'This article is top notch.',
					'published' => '1',
				),
			),
			2 => array(
				'Comment' => array(
					'id' => '3',
					'article_id' => '1',
					'comment' => 'Can we get more stuff like this from you?',
					'published' => '1',
				),
			),
			3 => array(
				'Comment' => array(
					'id' => '4',
					'article_id' => '2',
					'comment' => 'This is very similar to Testing an Article',
					'published' => '1',
				),
			),
			4 => array(
				'Comment' => array(
					'id' => '5',
					'article_id' => '1',
					'comment' => '<a href="http://google.com">Buy Viagra</a>',
					'published' => '0',
				),
			),
		);
		$this->assertEqual($expected,$result);

	}

/**
 * Test Searchable's exact match functionality
 */
	function testfilterConditionsExact() {
		// match a single record
		$params = $this->Article->filterConditions('Second Article', 'exaxt');
		$result = $this->Article->find('all', array('conditions' => array($params)));
		$expected = array(
			0 => array(
				'Article' => array(
					'id' => '2',
					'title' => 'Second Article',
					'body' => 'This is a test article',
					'published' => '1',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31',
				),
			),
		);
		$this->assertEqual($expected,$result);
		
		// match a record using a different field than the one used above
		$params = $this->Article->filterConditions('A unique string to match', 'exact');
		$result = $this->Article->find('all', array('conditions' => array($params)));
		$expected = array(
			0 => array(
				'Article' => array(
					'id' => 4,
					'title' => 'Fourth Article',
					'body' => 'A unique string to match',
					'published' => '1',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31'
				),
			),
		);
		$this->assertEqual($expected,$result);
		
		// match 2 records in different fields
		$params = $this->Article->filterConditions('Testing an Article', 'exact');
		$result = $this->Article->find('all', array('conditions' => array($params)));
		$expected = array(
			0 => array(
				'Article' => array(
					'id' => '1',
					'title' => 'Testing an Article',
					'body' => 'This is a test article',
					'published' => '1',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31',
				),
			),
			1 => array(
				'Article' => array(
					'id' => '3',
					'title' => 'Third Article',
					'body' => 'Testing an Article',
					'published' => '1',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31',
				),
			),
		);
		$this->assertEqual($expected,$result);
		
		// match 2 records in one field
		$params = $this->Article->filterConditions('This is a test article', 'exact');
		$result = $this->Article->find('all', array('conditions' => array($params)));
		$expected = array(
			0 => array(
				'Article' => array(
					'id' => '1',
					'title' => 'Testing an Article',
					'body' => 'This is a test article',
					'published' => '1',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31',
				),
			),
			1 => array(
				'Article' => array(
					'id' => '2',
					'title' => 'Second Article',
					'body' => 'This is a test article',
					'published' => '1',
					'created' => '2007-03-18 10:41:23',
					'updated' => '2007-03-18 10:43:31',
				),
			),
		);
		$this->assertEqual($expected,$result);
		
		// do not match phrases in a field
		$params = $this->Article->filterConditions('Article', 'exact');
		$result = $this->Article->find('all', array('conditions' => array($params)));
		$expected = array();
		$this->assertEqual($expected,$result);
	}
	
/**
 * Test Searchable's phrase match functionality
 */
	function testfilterConditionsPhrase() {
		// match a phrase inside a field
		$params = $this->Article->filterConditions('Third', 'phrase');
		$result = $this->Article->find('all', array('conditions' => array($params)));
		$expected = array(
			0 => array(
				'Article' => array(
					'id' => '3',
					'title' => 'Third Article',
					'body' => 'Testing an Article',
					'published' => '1',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31',
				),
			),
		);
		$this->assertEqual($expected,$result);
		
		// match an entire field
		$params = $this->Article->filterConditions('Third Article', 'phrase');
		$result = $this->Article->find('all', array('conditions' => array($params)));
		$expected = array(
			0 => array(
				'Article' => array(
					'id' => '3',
					'title' => 'Third Article',
					'body' => 'Testing an Article',
					'published' => '1',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31',
				),
			),
		);
		$this->assertEqual($expected,$result);
		
		// phrase match of two fields in different records
		$params = $this->Article->filterConditions('Testing', 'phrase');
		$result = $this->Article->find('all', array('conditions' => array($params)));
		$expected = array(
			0 => array(
				'Article' => array(
					'id' => '1',
					'title' => 'Testing an Article',
					'body' => 'This is a test article',
					'published' => '1',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31',
				),
			),
			1 => array(
				'Article' => array(
					'id' => '3',
					'title' => 'Third Article',
					'body' => 'Testing an Article',
					'published' => '1',
					'created' => '2007-03-18 10:43:23',
					'updated' => '2007-03-18 10:45:31',
				),
			),
		);
		$this->assertEqual($expected,$result);
	
		// do not match partial words
		$params = $this->Article->filterConditions('A uniqu', 'phrase');
		$result = $this->Article->find('all', array('conditions' => array($params)));
		$expected = array();
		$this->assertEqual($expected,$result);
	}
	
/**
 * Test Searchable while using a hasMany relationship
 */
	function testfilterConditionsHasMany() {
		// find records where comments match
		$this->ArticleHasMany->Comment->contain('ArticleHasMany');
		$params = $this->ArticleHasMany->filterConditions('I really like', 'phrase');
		$result = $this->ArticleHasMany->Comment->find('all', array('conditions' => array($params)));
		$expected = array(
			0 => array(
				'Comment' => array(
					'id' => '1',
					'article_id' => '1',
					'comment' => 'I really like your post, good job.',
					'published' => '1',
				),
				'ArticleHasMany' => array(
					'id' => '1',
					'title' => 'Testing an Article',
					'body' => 'This is a test article',
					'published' => '1',
					'created' => '2007-03-18 10:39:23',
					'updated' => '2007-03-18 10:41:31',
				),
			),
		);
		$this->assertEqual($expected,$result);
		
	}
}
