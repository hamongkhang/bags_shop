<?php

namespace wpie;

defined( 'ABSPATH' ) || exit;

class WPIE_Schedule {

        public function __construct() {
                add_filter( 'cron_schedules', array ( __CLASS__, 'cron_schedules' ), 99999, 1 );
        }

        public static function cron_schedules( $schedules = array () ) {

                return array_merge( self::get_schedules(), $schedules );
        }

        public static function get_schedules() {

                return array (
                        'wpie_30_min'     => array (
                                'interval' => 30 * MINUTE_IN_SECONDS,
                                'display'  => __( 'Every 30 Minutes', 'vj-wp-import-export' ),
                        ),
                        'wpie_hourly'     => array (
                                'interval' => HOUR_IN_SECONDS,
                                'display'  => __( 'Once Hourly', 'vj-wp-import-export' ),
                        ),
                        'wpie_twicedaily' => array (
                                'interval' => 12 * HOUR_IN_SECONDS,
                                'display'  => __( 'Twice Daily', 'vj-wp-import-export' ),
                        ),
                        'wpie_daily'      => array (
                                'interval' => DAY_IN_SECONDS,
                                'display'  => __( 'Once Daily', 'vj-wp-import-export' ),
                        ),
                        'wpie_weekly'     => array (
                                'interval' => WEEK_IN_SECONDS,
                                'display'  => __( 'Once Weekly', 'vj-wp-import-export' ),
                        ),
                        'wpie_monthly'    => array (
                                'interval' => MONTH_IN_SECONDS,
                                'display'  => __( 'Once Monthly', 'vj-wp-import-export' ),
                        ),
                );
        }

}
