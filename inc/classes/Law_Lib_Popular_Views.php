<?php
class Law_Lib_Popular_Views
{
    const TABLE_NAME = 'law_lib_popular_views';
    const TRANSIENT = 'law_lib_relation_popular_database';

    /**
     * @var bool
     */
    protected $prepared = false;
    /**
     * @var Law_Lib_Popular_Views
     */
    private static $instance = null;

    public function __construct()
    {
        if (!self::$instance) {
            $trans = get_transient(self::TRANSIENT);
            if (is_array($trans) && isset($trans['time']) && is_int($trans['time'])) {
                $this->prepared = ($trans['time'] + 3600) < time();
            }
            $this->prepare();
        }
        self::$instance = $this;
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Doing Preparation
     */
    protected function prepare()
    {
        if ( $this->prepared
             || ! apply_filters( 'law_lib_relation_popular_views_check_database', true )
        ) {
            return;
        }

        $this->prepared = true;
        global $wpdb;

        $table_popular = self::TABLE_NAME;
        $table_name = "{$wpdb->prefix}{$table_popular}";
        $prepare    = $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like( $table_name ) );
        set_transient(self::TRANSIENT, ['time' => time()]);

        if ( $wpdb->get_var( $prepare ) === $table_name ) {
            return;
        }
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `$table_name` (
    id BIGINT(20) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    post_id BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
    post_date_gmt DATE NOT NULL DEFAULT '0000-00-00' COMMENT 'ONLY DATE FOR GMT',
    view_count BIGINT(20) NOT NULL DEFAULT 0,
    INDEX `ll_view_index_post_id`(post_id),
    INDEX `ll_view_post_date_gmt`(post_date_gmt),
    INDEX `ll_view_view_count`(view_count),
    INDEX `ll_view_view_count_date`(view_count, post_date_gmt),
    UNIQUE INDEX `ll_unique_date_and_post_id`(post_id, post_date_gmt),
    CONSTRAINT `ll_post_id` FOREIGN KEY (`post_id`) REFERENCES `{$wpdb->prefix}posts`(`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=innodb CHARSET={$wpdb->charset} COLLATE={$wpdb->collate};
SQL;
        $wpdb->query( $sql );
    }

    /**
     * @param null|int|WP_Post $post_id
     *
     * @return bool|int
     */
    public function setData( $post_id = null )
    {
        $post_id = Law_Lib_Meta_Data::determinePostId( $post_id );
        if ( ! is_numeric( $post_id ) || $post_id < 1 ) {
            return false;
        }
        $post_id = get_post( $post_id );
        if (! $post_id instanceof WP_Post) {
            return false;
        }

        $time    = gmdate( 'Y-m-d' );
        $post_id = $post_id->ID;

        global $wpdb;

        $table_popular = self::TABLE_NAME;
        $table_name = "{$wpdb->prefix}{$table_popular}";
        $sql        = "SELECT `id`, `view_count` FROM {$table_name} WHERE `post_id` = '{$post_id}' AND `post_date_gmt` = '$time'";
        $var        = $wpdb->get_row( $sql );
        if ( ! empty( $var ) && isset( $var->id ) ) {
            $var->view_count = is_numeric( $var->view_count )
                ? $var->view_count
                : 0;
            $var->view_count = $var->view_count < 1 ? 0 : $var->view_count;
            $count           = absint( $var->view_count ) + 1;
            $sql             = "
        UPDATE {$table_name} SET
                `view_count`='{$count}'
            WHERE `id` = '{$var->id}'
        ";
        } else {
            $sql = "
            INSERT INTO {$table_name}(
                post_id,
                post_date_gmt,
                view_count
                ) VALUES(
                    '{$post_id}',
                     '{$time}',
                     '1'
                )
        ";
        }

        return $wpdb->query( $sql );
    }

    /**
     * @param null|int|WP_Post $post_id
     * @param string|int $dateRange
     *
     * @return false|int
     */
    public function countData( $post_id = null, $dateRange = '' )
    {
        $post_id = Law_Lib_Meta_Data::determinePostId( $post_id );
        if ( ! is_numeric( $post_id ) || $post_id < 1 ) {
            return false;
        }

        $post_id = get_post( $post_id );
        if ( ! $post_id instanceof WP_Post ) {
            return false;
        }
        $post_id   = $post_id->ID;
        $dateRange = is_string( $dateRange ) ? strtotime( $dateRange ) : null;
        $time      = is_int( $dateRange ) ? date( 'Y-m-d', $dateRange ) : gmdate( 'Y-m-d' );

        global $wpdb;
        $table_popular = self::TABLE_NAME;
        $table_name = "{$wpdb->prefix}{$table_popular}";
        $sql        = "SELECT id, `view_count` FROM {$table_name} WHERE post_id = '{$post_id}' AND post_date_gmt = '$time'";
        $var        = $wpdb->get_var( $sql );
        $var        = $var ?? false;

        return is_numeric( $var ) ? absint( $var ) : false;
    }

    /**
     * @param int $day
     * @param int $count
     * @param int $page
     *
     * @return stdClass
     */
    public function getViews(int $day = 30, int $count = 5, int $page = 1): stdClass
    {
        global $wpdb;

        $day_default  = apply_filters(
            'law_lib_relation_meta_query_popular_post_days',
            30
        );
        $day_default            = ! is_numeric( $day ) ? 30 : $day_default;
        $day                    = ! is_numeric( $day ) ? $day_default : $day;
        $day                    = absint( $day );
        $day                    = $day < 1 ? 1 : $day;
        $time                   = date( 'Y-m-d', strtotime( gmdate( 'Y-m-d H:i:s' ) ) - ( $day * DAY_IN_SECONDS ) );
        $count                  = $count < 1 ? 1 : $count;
        $page                   = $page < 1 ? 1 : $page;
        $offset                 = ( $page - 1 ) * $count;
        $table_popular = self::TABLE_NAME;
        $table_name = "{$wpdb->prefix}{$table_popular}";
        $query                  = "
        SELECT
               (
                   SELECT MAX(c.count) as found_posts
                   from (
                    SELECT
                          count(cb.post_id) as count
                        FROM
                            `$table_name` cb
                            INNER JOIN wp_posts w on (
                                w.ID = cb.post_id
                                and w.post_status = 'publish'
                            )
                        WHERE
                            cb.`post_date_gmt` >= '$time'
                        GROUP BY cb.`post_id`
                    ) as c
               ) as found_posts,
               (
                       SELECT
                            SUM(`at`.`view_count`) 
                        FROM 
                             `$table_name` as at
                        INNER JOIN wp_posts w on (
                            w.ID = at.post_id AND w.post_status = 'publish'
                        )
                        WHERE at.`post_date_gmt` >= '$time'
               ) as `total_views`,
               (
                   SELECT
                        SUM(`view_count`) 
                        FROM `$table_name` as bt
                    WHERE
                        (tb.post_id = bt.post_id AND bt.`post_date_gmt` >= '$time')
               ) as post_views,
            tb.`post_id`
        FROM `$table_name` as tb
            INNER JOIN wp_posts wp on (
                wp.ID = tb.post_id AND wp.post_status = 'publish'
            )
            WHERE 
                tb.post_date_gmt >= '$time'
            GROUP BY 
                tb.post_id
            LIMIT {$count} OFFSET {$offset}
    ";
        $std                    = new stdClass();
        $std->current_page      = $page;
        $std->total_page        = 0;
        $std->found_posts       = 0;
        $std->total_found_posts = 0;
        $std->total_views       = 0;
        $std->posts             = [];
        $post_ids               = [];
        foreach ( $wpdb->get_results( $query, ARRAY_A ) as $result ) {
            $std->total_found_posts         = absint( $result['found_posts'] );
            $std->total_views               = absint( $result['total_views'] );
            $post_ids[ $result['post_id'] ] = $result;
        }
        if (count($post_ids) < $count) {
            $args = [
                'post__not_in'        => array_keys( $post_ids ),
                'posts_per_page'      => $count - count($post_ids),
                'numberposts'         => $count - count($post_ids),
                'current_page'        => 1,
                'post_type'           => 'post',
                'post_status'         => 'publish',
                'has_password'        => false,
                'ignore_sticky_posts' => true,
                'suppress_filters'    => true,
                'no_found_rows'       => true,
                'order'               => 'desc',
                'orderBy'             => 'date',
            ];
            $query                  = new WP_Query;
            $_posts                 = $query->query($args);
            if (empty($post_ids)) {
                $std->total_found_posts = $query->found_posts;
            } else {
                $std->total_found_posts += count($_posts);
            }
            foreach ($_posts as $post) {
                $post_ids[$post->ID] = (array) $post;
            }
        }

        // $post_ids = array_flip($post_ids);
        $std->total_found_posts = count($post_ids);
        if ( ! empty( $post_ids ) ) {
            $args = [
                'post__in'            => array_keys( $post_ids ),
                'ignore_sticky_posts' => true,
                'suppress_filters'    => true,
                'no_found_rows'       => true,
                'numberposts'         => $std->total_found_posts,
            ];
            foreach ( get_posts( $args ) as $post ) {
                $post->views             = $post_ids[ $post->ID ]['post_views'] ?? 0;
                $std->posts[ $post->ID ] = $post;
            }
            $std->found_posts = count( $std->posts );
        }
        uasort( $std->posts, function ( $a, $b ) {
            if ( $a->views === $b->views ) {
                return 0;
            }

            return $a->views > $b->views ? - 1 : 1;
        } );
        $std->total_page = $std->total_found_posts ? ceil( $std->total_found_posts / $count ) : 0;

        return $std;
    }
}