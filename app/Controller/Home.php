<?php

namespace Controller;

use Model\Links;
use SugiPHP\Sugi\Cache;

class Home
{
	public function indexAction()
	{
		$modelLinks = new Links();
		if (!$link = Cache::get("link1")) {
			$link = $modelLinks->getLink(1);
			Cache::set("link1", $link, 10); // caches the link for 10 seconds
		}
		echo "<a href=\"{$link['uri']}\">{$link['title']}</a>";
	}
}
