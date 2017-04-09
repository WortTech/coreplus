<?php
/**
 * [PAGE DESCRIPTION HERE]
 *
 * @package Core
 * @since 1.9
 * @author Charlie Powell <charlie@evalagency.com>
 * @copyright Copyright (C) 2009-2017  Charlie Powell
 * @license GNU Affero General Public License v3 <http://www.gnu.org/licenses/agpl-3.0.txt>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/agpl-3.0.txt.
 */

/**
 * A simple class to encapsulate the catch 404 logic and lookup the mapped URL.
 *
 * This is bound and called with the hook <code>/core/page/error-404</code>
 *
 * @package Core
 * @author Charlie Powell <charlie@evalagency.com>
 */
abstract class RewriteMapHelper {
	public static function Catch404Hook(View $view){

		$request = PageRequest::GetSystemRequest();

		// All the exact matches, in the order of precedence.
		$exactmatches = [];

		// The first search I want do is for the full URL exactly as submitted.
		// This is because the user can submit URLs with GET parameters attached to them.
		// It needs to act in a google-esque manner, where if the user requested x=1&y=2... then give them x=1 and y=2!
		$exactmatches[] = '/' . substr($request->uri, strlen(ROOT_WDIR));

		// This one is the resolved URL, without any GET parameters.  It's still a very common and very specific rewrite choice.
		$exactmatches[] = $request->uriresolved;

		// Now, look for them!
		foreach($exactmatches as $incomingurl){
			// Look for it!
			$maps = RewriteMapModel::Find(array('rewriteurl' => $incomingurl));

			// Did I get one did I get one did I get one?
			if(sizeof($maps)){
				// Grab the first one, that'll be the latest, (should multiple exist.... somehow :/ )
				$match = $maps[0]->get('baseurl');

				// Resolve that to the new rewriteurl and redirect!
				$newpage = PageModel::Construct($match);
				\core\redirect($newpage->get('rewriteurl'), 301);
			}
		}


		// Else, no match was found... maybe it's a fuzzy page!
		// Since this page will have no longer existed, I can't just use the builtin logic :(
		$fuzzy = $request->uriresolved;
		do{
			$fuzzy = substr($fuzzy, 0, strrpos($fuzzy, '/'));

			$fuzzymaps = RewriteMapModel::Find(array('rewriteurl' => $fuzzy, 'fuzzy' => '1'));
			if(sizeof($fuzzymaps)){
				// Yay!
				// Don't forget to throw on the rest of the url.
				$match = $fuzzymaps[0]->get('baseurl');
				$newpage = PageModel::Construct($match);
				$url = $newpage->get('rewriteurl');
				if($newpage->get('fuzzy')){
					// Only if the new page is fuzzy too.
					$url .= substr($incomingurl, strlen($fuzzy));
				}
				\core\redirect($url, 301);
			}
		}
		while($fuzzy);

		// Sigh, guess this page didn't make the cut.
		// There is no return necessary, this hook will simply silently continue to the next.
	}
}
