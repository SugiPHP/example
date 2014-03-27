<?php

namespace Controller;

use Model\Links;
use SugiPHP\Sugi\Cache;

class Home
{
	public function indexAction()
	{
		$modelLinks = new Links();
		// grab all links
		if (!$links = Cache::get("links")) {
			$links = $modelLinks->getLinks();
			Cache::set("links", $links, 30); // cache all links for 30 seconds;
		}
		echo "<ul>";
		foreach ($links as $link) {
			echo "<li><a href=\"{$link['uri']}\">{$link['title']}</a></li>";
		}
		echo "</ul>";

		// // or grab only one
		// if (!$link = Cache::get("link1")) {
		// 	$link = $modelLinks->getLink(1);
		// 	Cache::set("link1", $link, 10); // caches the link for 10 seconds
		// }
		// echo "<a href=\"{$link['uri']}\">{$link['title']}</a>";
	}
}
