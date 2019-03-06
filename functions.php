<?php
/**
 * Utility functions
 *
 * @package GoogleAnalyticsReports
 */

/**
 * Get plugin version
 *
 * @since 1.0.0
 * @return string
 */
function gar_version() {
	static $info = null;
	if ( is_null( $info ) ) {
		$info = get_file_data( __DIR__ . '/google-analytics-reports.php', [
			'version' => 'Version',
		] );
	}

	return $info['version'];
}

/**
 * Get reports
 *
 * @since 1.0.0
 * @param array $args
 * @return \WP_Error|\Google_Service_AnalyticsReporting_GetReportsResponse
 */
function gar_reports( $args = [] ) {
	$reports = GoogleAnalyticsReports\Analytics::get_instance()->get_report( $args );

	return $reports;
}

/**
 * Get reports in post
 *
 * @since 1.0.0
 * @param array $args
 * @return array
 */
function gar_report_posts( $args = [] ) {
	$posts = [];

	$reports  = gar_reports( $args );

	if ( is_wp_error( $reports ) ) {
		return $posts;
	}

	$page_path_index = ( ! empty( $args['page_path_index'] ) ? $args['page_path_index'] : 0 );

	$report = $reports[0];
	$rows   = $report->getData()->getRows();
	for ( $i = 0; $i < count( $rows ); $i ++ ) {
		$row        = $rows[ $i ];
		$dimensions = $row->getDimensions();
		$metrics    = $row->getMetrics();
		$values     = $metrics[0]->getValues();

		$_post = gar_url_to_post( $dimensions[ $page_path_index ] );
		if ( empty( $_post ) ) {
			$_post = new \stdClass();
		}
		$_post->dimensions = $dimensions;
		$_post->metrics    = $values;

		$posts[] = $_post;
	}

	return $posts;
}

/**
 * Get post from URL
 *
 * @since 1.0.0
 * @param string $url
 * @return null|WP_post
 */
function gar_url_to_post( $url ) {
	$post_id = url_to_postid( $url );
	if ( empty( $post_id ) ) {
		return null;
	}

	return get_post( $post_id );
}
