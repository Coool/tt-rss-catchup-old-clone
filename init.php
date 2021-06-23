<?php

class Af_Catchup_Old extends Plugin {

	/** @var PluginHost $host */
	private $host;

	const ACTION_ONE_DAY = "action_force_catchup_1day";
	const ACTION_ONE_WEEK = "action_force_catchup_1week";
	const ACTION_ONE_MONTH = "action_force_catchup_1month";

	function about() {
		return [1.0,
			"Provides a filter action to mark old articles as read, based on feed-provided timestamp",
			"fox"];
	}

	function init($host) {
		$this->host = $host;

		$this->host->add_filter_action($this, self::ACTION_ONE_DAY, __("Older than 1 day"));
		$this->host->add_filter_action($this, self::ACTION_ONE_WEEK, __("Older than 1 week"));
		$this->host->add_filter_action($this, self::ACTION_ONE_MONTH, __("Older than 1 month"));
	}

	function hook_article_filter_action($article, $action) {
		$cutoff_timestamp = 0;

		switch ($action) {
			case self::ACTION_ONE_DAY:
				$cutoff_timestamp = time() - 86400;
				break;
			case self::ACTION_ONE_WEEK:
				$cutoff_timestamp = time() - 86400 * 7;
				break;
			case self::ACTION_ONE_MONTH:
				$cutoff_timestamp = time() - 86400 * 31;
				break;
		}

		if ($cutoff_timestamp) {
			Debug::log(sprintf("[af_catchup_old] article timestamp: %s, cutoff timestamp: %s ",
				date("Y-m-d H:i:s", $article["timestamp"]),
				date("Y-m-d H:i:s", $cutoff_timestamp)));

			if ($article["timestamp"] < $cutoff_timestamp) {
				Debug::log("[af_catchup_old] article is older than cutoff, marking it as read.");
				$article["force_catchup"] = true;
			} else {
				Debug::log("[af_catchup_old] article is newer than cutoff, ignoring.");
			}
		}

		return $article;
	}

	function api_version() {
		return 2;
	}
}
