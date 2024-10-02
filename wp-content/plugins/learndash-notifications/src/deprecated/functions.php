<?php
/**
 * Deprecated functions file.
 *
 * @package LearnDash\Notifications\Deprecated
 */

/**
 * The main function for returning the plugin instance.
 *
 * @deprecated 1.6.3
 *
 * @since 1.0
 *
 * @return LearnDash_Notifications The one and only true instance.
 */
function learndash_notifications() {
	return LearnDash_Notifications::instance();
}
