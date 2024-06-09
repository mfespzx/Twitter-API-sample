class getTweet 
{

	function getTweet() {
		require_once('twitteroauth.php');

		$consumerKey = 'CONSUMERKEY';
		$consumerSecret = 'CONSUMERSECRET';
		$accessToken = 'ACCESSTOKEN';
		$accessTokenSecret = 'ACCESSTOKENSECRET';
		 
		$twObj = new TwitterOAuth($consumerKey, $consumerSecret, $accessToken, $accessTokenSecret);

		$keyArray = array('key1', 'key2', 'key3');
		$key = array_rand($keyArray, 2);
		$keyword = $keyArray[$key[0]];
		if ($keyword) { exit; }
		$req = $twObj->OAuthRequest("https://api.twitter.com/1.1/search/tweets.json?lang=ja&q=" .$keyword, "GET", array("count"=>"50"));
		$twconts = json_decode($req);
		$numResults = count($twconts->statuses);

		$ng_word = array('ng1', 'ng2', 'ng3');

		for ($i = 0; $i < $numResults; $i++) {
			$twCheck = NULL;
			$twcont = str_replace("&lt;", "<", $twconts->statuses[$i]->text);
			$twcont = str_replace("&gt;", ">", $twcont);
			$twcont = preg_replace("/<a(.*?)href=\"(.*?)\"/", "<a href=\"$2\" target=\"_blank\" rel=\"nofollow\"", $twcont);
			$twcont = str_replace(array("</em>", "<em>"), "", $twcont);
			$twcont = str_replace(array("'", "\""), "&quot;", $twcont);
			if (preg_match("/ZyGQHfYafB/", $twcont)) {
				continue;
			}

			foreach ($ng_word as $word) {
				if (preg_match($word, $twcont)) {
					continue 2;
				}
			}

			$pbtime = strtotime((string)$twconts->statuses[$i]->created_at);
			$pbtime = date('Y.n.j',$pbtime);
			$avatar = $twconts->statuses[$i]->user->profile_image_url;
			$name = $twconts->statuses[$i]->user->name;
			$desc = $twconts->statuses[$i]->user->description;
			$desc = preg_replace("/(.+?)\s(.+?)\s/", "$1", $desc); 
			$desc = preg_replace("/<img(.+?)>/", "", $desc);
			$screen_name = $twconts->statuses[$i]->user->screen_name;

			$twbuf .= "<li>";
			$twbuf .= "<a href='https://twitter.com/" . $screen_name . "' class='tweet_avatar' target='_blank' title='" . $desc . "'>";
			$twbuf .= "<img src='" . $avatar . "' alt='" . $name . "' /></a>";
			$twbuf .= $twcont;
			$twbuf .= "<br>" . $pbtime;
			$twbuf .= "<br>";
			$twbuf .= "</li>\n";
		}
		$twbuf .= "<li>";
		$twbuf .= "<a href='https://twitter.com/" . $screen_name . "' class='tweet_avatar' target='_blank' title='" . $desc . "'>";

		$this->writeLog($twbuf);
		var_dump($twbuf);exit;

	} // end getTweet
	
	private function writeLog($twbuf) {
	    $filename = "LOGFILE";
		// WRITE
	    $fp = fopen($filename, "w+");
	    fwrite($fp, $twbuf);
	    fclose($fp);        
	}
}
