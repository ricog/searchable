<?php
class CommentFixture extends CakeTestFixture {
	var $name = 'Comment';
	 
	var $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'), 
		'article_id' => array('type' => 'integer'), 
		'comment' => 'text', 
		'published' => array('type' => 'integer', 'default' => '0', 'null' => false), 
	);
	var $records = array(
		array ('id' => 1, 'article_id' => 1, 'comment' => 'I really like your post, good job.', 'published' => '1'),
		array ('id' => 2, 'article_id' => 3, 'comment' => 'This article is top notch.', 'published' => '1'),
		array ('id' => 3, 'article_id' => 1, 'comment' => 'Can we get more stuff like this from you?', 'published' => '1'),
		array ('id' => 4, 'article_id' => 2, 'comment' => 'This is very similar to Testing an Article', 'published' => '1'),
		array ('id' => 5, 'article_id' => 1, 'comment' => '<a href="http://google.com">Buy Viagra</a>', 'published' => '0'),
		);
}
?>