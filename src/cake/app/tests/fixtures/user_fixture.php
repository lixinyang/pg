<?php
/* User Fixture generated on: 2011-05-20 14:37:48 : 1305873468 */
class UserFixture extends CakeTestFixture {
	var $name = 'User';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 80, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'cookie_tooken' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 80, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'support_project' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'cookie_tooken_before_login' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 80, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'cookie_tooken_UNIQUE' => array('column' => 'cookie_tooken', 'unique' => 1), 'cookie_tooken_before_login_UNIQUE' => array('column' => 'cookie_tooken_before_login', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'cookie_tooken' => 'Lorem ipsum dolor sit amet',
			'support_project' => 1,
			'cookie_tooken_before_login' => 'Lorem ipsum dolor sit amet'
		),
	);
}
?>