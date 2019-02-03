<?php


namespace QNews\src\services\data;


use WP_Query;

abstract class TD_QNews_BaseDataTables
{

    abstract function getData();
    protected function getPosts($params, $return_query = true) {
            $params = wp_parse_args($params,
                array(
                    'post_type' => 'post',
                    'posts_per_page' => '-1',
                    'category_name' => '',
                    'author_id' => '',
                    'tag' => '',
                    'reaction' => '',
                    'post_in' => '',
                    'post_not_in' => '',
                    'sort' => '',
                    'order' => '',
                    'offset' => '0',
                    'paged' => '',
                    'display_pagination' => 'no',
                    'pagination_type' => '',
                    'pagination_range_limit' => '',
                    'post_status' => 'publish',
                    'only_videos' => '',
                    'next_page' => '1'
                ));
            $query_array = array();
            if (isset($params['year']) && isset($params['monthnum']) && $params['year']!=0 && $params['monthnum']!=0) {
                $query_array['year'] = $params['year'];
                $query_array['monthnum'] = $params['monthnum'];
            }

            $query_array['post_type']=$params['post_type'];
            $query_array['post_status'] = $params['post_status']; //to ensure that ajax call will not return 'private' posts
            if(!empty($params['tax_query']))
            {
                $query_array['tax_query'] = $params['tax_query'];
            }
            if ($params['category_name'] !== '') {
                $query_array['category_name'] = $params['category_name'];
            }

            if ($params['author_id'] !== '') {
                $query_array['author_id'] = str_replace(' ', '', $params['author_id']); //because of the data and quotes spaces need to be erased
                $query_array['author'] = str_replace(' ', '', $params['author_id']); //because of the data and quotes spaces need to be erased
            }

            if (!empty($params['tag'])) {
                $query_array['tag'] = str_replace(' ', '', $params['tag']);
            }
            if (!empty($params['post_not_in'])) {

                $query_array['post__not_in'] = explode(",", $params['post_not_in']);
                if (!$return_query) {
                    $query_array['post_not_in'] = str_replace(' ', '', $params['post_not_in']);
                }
            }
            if (!empty($params['post_in'])) {
                $query_array['post__in'] = explode(",", $params['post_in']);
                if (!$return_query) {
                    $query_array['post_in'] = str_replace(' ', '', $params['post_in']);
                }
            }

            if ($params['only_videos'] == 'yes') {
                $query_array['tax_query'] = array(
                    array(
                        'taxonomy' => 'post_format',
                        'field'    => 'slug',
                        'terms'    => array( 'post-format-video' ),
                    )
                );

                if (!$return_query) {
                    $query_array['only_videos'] = 'yes';
                }
            }

            $query_array['ignore_sticky_posts'] = '1';

            switch ($params['sort']) {
                case 'latest':
                    $query_array['orderby'] = 'date';
                    break;

                case 'random':
                    $query_array['orderby'] = 'rand';
                    break;

                case 'random_today':
                    $query_array['orderby'] = 'rand';
                    $query_array['year'] = date('Y');
                    $query_array['monthnum'] = date('n');
                    $query_array['day'] = date('j');
                    break;

                case 'random_seven_days':
                    $query_array['date_query'] = array(
                        'column' => 'post_date_gmt',
                        'after' => '1 week ago'
                    );
                    $query_array['orderby'] = 'rand';
                    break;

                case 'comments':
                    $query_array['orderby'] = 'comment_count date';
                    $query_array['order'] = 'DESC';
                    break;

                case 'title':
                    $query_array['orderby'] = 'title';
                    break;

                case 'popular':
                    $query_array['meta_key'] = 'qode_count_post_views_meta';
                    $query_array['orderby'] = 'meta_value_num date';
                    $query_array['order'] = 'DESC';
                    break;

                case 'featured_first':
                    //to get posts by featured, and afterwards when featured is not set
                    $query_array['meta_query'] = array(
                        'relation' => 'OR',
                        'featured' => array(
                            'key' => 'qode_news_post_featured_meta',
                            'value' => 'a',
                            'compare' => '>'
                        ),
                        'rest' => array(
                            'key' => 'qode_news_post_featured_meta',
                            'value' => 'exists',
                            'compare' => 'NOT EXISTS'
                        )
                    );

                    $query_array['order'] = 'DESC';
                    $query_array['orderby'] = 'meta_value date';

                    if (!$return_query) {
                        $query_array['orderby'] = 'meta_value,date';
                    }
                    break;

                case 'trending_first':
                    //to get posts by trending, and afterwards when trending is not set
                    $query_array['meta_query'] = array(
                        'relation' => 'OR',
                        'trending' => array(
                            'key' => 'qode_news_post_trending_meta',
                            'value' => 'a',
                            'compare' => '>'
                        ),
                        'rest' => array(
                            'key' => 'qode_news_post_trending_meta',
                            'value' => 'exists',
                            'compare' => 'NOT EXISTS'
                        )
                    );

                    $query_array['order'] = 'DESC';
                    $query_array['orderby'] = 'meta_value date';

                    if (!$return_query) {
                        $query_array['orderby'] = 'meta_value,date';
                    }
                    break;

                case 'hot_first':
                    //to get posts by hot, and afterwards when hot is not set
                    $query_array['meta_query'] = array(
                        'relation' => 'OR',
                        'hot' => array(
                            'key' => 'qode_news_post_hot_meta',
                            'value' => 'a',
                            'compare' => '>'
                        ),
                        'rest' => array(
                            'key' => 'qode_news_post_hot_meta',
                            'value' => 'exists',
                            'compare' => 'NOT EXISTS'
                        )
                    );

                    $query_array['order'] = 'DESC';
                    $query_array['orderby'] = 'meta_value date';

                    if (!$return_query) {
                        $query_array['orderby'] = 'meta_value,date';
                    }
                    break;

                case 'reactions':
                    if ($params['reaction'] !== ''){

                        if (!$return_query) {
                            $query_array['reaction'] = $params['reaction'];
                        }

                        //to get posts by featured, and afterwards when featured is not set
                        $query_array['meta_query'] = array(
                            'relation' => 'OR',
                            'featured' => array(
                                'key' => 'qode_news_reaction_'.$params['reaction'],
                                'value' => '-1',
                                'compare' => '>',
                                'type' => 'NUMERIC'
                            ),
                            'rest' => array(
                                'key' => 'qode_news_reaction_'.$params['reaction'],
                                'value' => 'exists',
                                'compare' => 'NOT EXISTS'
                            )
                        );

                        $query_array['order'] = 'DESC';
                        $query_array['orderby'] = 'meta_value date';

                        if (!$return_query) {
                            $query_array['orderby'] = 'meta_value,date';
                        }
                    } else {
                        $query_array['order'] = 'DESC';
                        $query_array['orderby'] = 'date';
                    }
                    break;
            }

            $query_array['posts_per_page'] = $params['posts_per_page'];

            if (!empty($params['order'])) {
                $query_array['order'] = $params['order'];
            }
            if (!empty($params['orderby'])) {
                $query_array['orderby'] = $params['orderby'];
            }

            if (!$return_query) {
                $query_array['sort'] = $params['sort'];
            }


            if ( ! empty( $params['next_page'] ) ) {
                $query_array['paged'] = $params['next_page'];
            } else {
                $query_array['paged'] = 1;
            }

            if (!empty($params['offset'])) {
                if ($query_array['paged'] > 1) {
                    $query_array['offset'] = $params['offset'] + (($params['paged'] - 1) * $params['posts_per_page']);
                } else {
                    $query_array['offset'] = $params['offset'];
                }
            }

            $list_query = new WP_Query($query_array);
            if (!empty($params['offset']) && $params['offset'] > '0') {
                $list_query->max_num_pages = ceil(($list_query->found_posts - $params['offset']) / $params['posts_per_page']);
            }

            $query_array['max_num_pages'] = $list_query->max_num_pages;

            if ($return_query) {
                return $list_query;
            } else {
                return $query_array;
            }
        }

}