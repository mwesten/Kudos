<?php defined('APP_PATH') or die('No direct script access.');


class helpers {

	public static function unslug($text)
	{
		return str_replace('-', ' ', $text);
	}

	public static function markdown_file($file)
	{
		return static::markdown(static::restricted_file_get_contents($file));
	}	

	public static function markdown($string)
	{
		require_once(APP_PATH.'/libraries/markdown.php');

		return markdown($string);
	}		

	public static function restricted_file_get_contents($file)
	{
		if(strpos(realpath($file), Config::get('kudos.content_path')) !== false)
		{
			return file_get_contents($file);
		}
		else
		{
			return '';
		}
	}

	public static function articles($count='*'){
		// Get all the articles
		$articles = glob(Config::get('kudos.content_path')."/published/*.markdown");

		if($articles){
			// Sort them newest to oldest
			rsort($articles);

			// Limit to the number required
			if( $count != '*') $articles = array_slice($articles, 0, $count);

			// Create the article detail array
			return array_map(function($path)
			{
				// Get path parts
				$parts = pathinfo($path);

				// Find just the date
				$date = substr($parts['filename'], 0, 8);

				return array(
						'path'	=> $path,
						'year'	=> $year = substr($date, 0, 4),
						'month' => $month = substr($date, 4, 2),
						'day'	=> $day = substr($date, 6, 2),
						'title' => trim(helpers::unslug(substr($parts['filename'], 8))),
						'link'	=> URL::to() . $year.'/'.$month.'/'.$day.'/'.ltrim(substr($parts['filename'], 8),'-'),
					);
			}, $articles);
		}
	}

	public static function pages(){
		// Get all the articles
		$pages = glob(Config::get('kudos.content_path')."/pages/*.markdown");

		if($pages){
			// Sort them newest to oldest
			sort($pages);

			// Create the article detail array
			return array_map(function($path)
			{
				// Get path parts
				$parts = pathinfo($path);

				return array(
						'path'	=> $path,
						'title' => trim(helpers::unslug($parts['filename'])),
						'link'	=> URL::to() . 'page/'.$parts['filename'],
					);
			}, $pages);
		}
	}	
}