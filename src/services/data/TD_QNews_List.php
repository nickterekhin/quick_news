<?php


namespace QNews\src\services\data;


class TD_QNews_List extends TD_QNews_BaseDataTables
{
    private $response_data = array(
        'draw'=>0,
        'recordsTotal'=>0,
        'recordsFiltered'=>0,
        'data'=>array()
    );

    function getData()
    {
        global $wpdb;
        $page = (isset($_REQUEST['start']) && isset($_REQUEST['length']))?isset($_REQUEST['start'])/isset($_REQUEST['length']):0;
        $attr = array(
            'posts_per_page'=>isset($_REQUEST['length'])?$_REQUEST['length']:0,
            'paged'=>isset($_REQUEST['start'])?$_REQUEST['start']:0
        );

        $columns = $_REQUEST['columns'];
        $order = $_REQUEST['order'];
        $search = $_REQUEST['search']['value'];

        $searchText = '';
        if(isset($search) && !empty($search))
            $searchText = ' AND (p.post_title LIKE "%'.$search.'%" OR p.post_date LIKE "%'.$search.'%") ';

        $order_direction = 'ORDER BY p.post_date DESC';

        $filter_category_id = $columns[3]['search']['value'];

        $filter_term = '';
        if(isset($filter_category_id) && !empty($filter_category_id))
                $filter_term = ' INNER JOIN wp_term_relationships tr ON p.ID = tr.object_id INNER JOIN wp_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.term_id = '.$filter_category_id;



        $filter_date_range = $columns[2]['search']['value'];

        $filter_by_date_range = '';
        if(isset($filter_date_range) && !empty($filter_date_range)) {
            $dates = $this->parseDate($filter_date_range);
            if($dates['start_date'])
            {
                $filter_by_date_range .= ' AND p.post_date >="'.$dates['start_date'].'" ';
            }
            if($dates['end_date'])
            {
                $filter_by_date_range .= ' AND p.post_date <="'.$dates['end_date'].'" ';
            }

        }

        $res = $wpdb->get_results("SELECT p.ID,p.post_title,p.post_date , IFNULL(pm.meta_value,'no') as isFeatured,IFNULL(pm1.meta_value,0) as Views FROM wp_posts p  
LEFT JOIN wp_postmeta pm ON p.ID = pm.post_id AND pm.meta_key = 'qode_news_post_featured_meta' 
LEFT JOIN wp_postmeta pm1 ON p.ID = pm1.post_id AND pm1.meta_key = 'qode_count_post_views_meta'
{$filter_term} WHERE p.post_type='post' AND post_status='publish' {$searchText} {$filter_by_date_range} {$order_direction}  LIMIT ".$attr['paged'].','.$attr['posts_per_page'],ARRAY_A);
        $new_posts = array();

        foreach($res as $p)
        {
            $new_posts[] = $this->mapPost($p);
        }

        //$q = $this->getPosts($attr);
        $response_data['draw'] = $_REQUEST['draw'];
        $response_data['data'] = $new_posts;
        $res = $wpdb->get_var("SELECT COUNT(*) as Total FROM wp_posts p {$filter_term}  WHERE p.post_type='post' AND post_status='publish' {$searchText} {$filter_by_date_range} ");
        $response_data['recordsTotal'] =$res;
        $response_data['recordsFiltered'] =$res;
    return json_encode($response_data);

    }

    private function mapPost($post)
    {
        $post['link'] = get_permalink($post['ID']);
        $post['categories'] = get_the_category($post['ID']);

        return $post;
    }

    private function parseDate($date_value)
    {
        $matches = array();
        $dates = array(
            'start_date'=>null,
            'end_date'=>null
        );
        if(preg_match('/((0?[1-9]|[12][0-9]|3[01])[- \/.](0?[1-9]|[12][0-9]|3[01])[- \/.]((?:19|20)[0-9]{2}))\]:\[((0?[1-9]|[12][0-9]|3[01])[- \/.](0?[1-9]|[12][0-9]|3[01])[- \/.]((?:19|20)[0-9]{2}))?/',$date_value,$matches)==1)
        {
            if(isset($matches[1]) && !empty($matches[1]))
                $dates['start_date'] = $matches[4].'-'.$matches[3].'-'.$matches[2];

            if(isset($matches[5]) && !empty($matches[5]))
                $dates['end_date'] = $matches[8].'-'.$matches[7].'-'.$matches[6];


        }
        return $dates;

    }


}