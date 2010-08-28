<?php
class ArticleFixture extends CakeTestFixture {
	var $name = 'Article';
	 
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'), 
		'title' => array('type' => 'string', 'length' => 255, 'null' => false), 
		'body' => 'text', 
		'published' => array('type' => 'integer', 'default' => '0', 'null' => false), 
		'created' => 'datetime', 
		'updated' => 'datetime' 
	);
	var $records = array(
		array ('id' => 1, 'title' => 'Testing an Article', 'body' => 'This is a test article', 'published' => '1', 'created' => '2007-03-18 10:39:23', 'updated' => '2007-03-18 10:41:31'),
		array ('id' => 2, 'title' => 'Second Article', 'body' => 'This is a test article', 'published' => '1', 'created' => '2007-03-18 10:41:23', 'updated' => '2007-03-18 10:43:31'),
		array ('id' => 3, 'title' => 'Third Article', 'body' => 'Testing an Article', 'published' => '1', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'),
		array ('id' => 4, 'title' => 'Fourth Article', 'body' => 'A unique string to match', 'published' => '1', 'created' => '2007-03-18 10:43:23', 'updated' => '2007-03-18 10:45:31'),
		);
}
?>