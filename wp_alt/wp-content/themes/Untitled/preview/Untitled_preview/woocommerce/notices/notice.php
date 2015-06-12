<?php
    if (!defined('ABSPATH'))
        exit; // Exit if accessed directly
    if (!$messages)
        return;

	foreach($messages as $message) {
		theme_information_message($message);
	}
?>