<?php

namespace Controller;

use Model\Links;

class Home
{
	public function indexAction()
	{
		$modelLinks = new Links();
		$link = $modelLinks->getLink(1);
		echo "<a href=\"{$link['uri']}\">{$link['title']}</a>";
	}
}
