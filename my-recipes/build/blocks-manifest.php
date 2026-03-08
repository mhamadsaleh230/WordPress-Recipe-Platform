<?php
// This file is generated. Do not modify it manually.
return array(
	'my-recipes' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'create-block/my-recipes',
		'version' => '0.1.0',
		'title' => 'My Recipes',
		'category' => 'widgets',
		'icon' => 'smiley',
		'description' => 'Example block scaffolded with Create Block tool.',
		'example' => array(
			
		),
		'supports' => array(
			'html' => false
		),
		'textdomain' => 'my-recipes',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'render' => 'file:./render.php',
		'viewScript' => 'file:./view.js',
		'attributes' => array(
			'headingtext' => array(
				'type' => 'string',
				'default' => 'All Recipes'
			),
			'ingredients' => array(
				'type' => 'string'
			),
			'recipedate' => array(
				'type' => 'string',
				'default' => ''
			),
			'cuisine' => array(
				'type' => 'string',
				'default' => ''
			),
			'type' => array(
				'type' => 'string',
				'default' => ''
			)
		)
	)
);
