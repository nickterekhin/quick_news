<?php
?>
<script type="text/javascript">
    (function($){
        $.notify.addStyle('tdqn',{
            html:'<div><span><i class="fas fa-newspaper"></i><span data-notify-text></span></span></div>'
        });
        $(document).ready(function(){
            managedClisk('td_qn-cntrl');
            managedClisk('td_qn-close');

            $(".td_qn-show-news-feed").notify("Лента Новостей",{position:'left',style:'tdqn'});
            td_qn_tables.init_table("#td_qn-table",'<?php echo admin_url('admin-ajax.php'); ?>',getColumns());
            td_qn_tables.initFilter({container:"#td_qn-filter-by-category",column:3});
            td_qn_tables.initFilterDateRange({start_date:"#td_qn-filter-start-date",end_date:"#td_qn-filter-end-date",column:2});
        });
        function getColumns()
        {
            let arrColumns = [];
            let index = 0;
            arrColumns.push({ "data": "ID", "targets": index++,'visible':false });
            arrColumns.push({ "data": "post_title", "targets": index++,"orderable":false, "render": function (source, type, row, meta) {
                let html = '';
                let dt = new Date(row.post_date),today = new Date();
                let date_format = dt.format('MM-dd-yyyy HH:m');

                if(dt === today)
                    date_format = dt.format('HH:m');
                let is_featured =  '';
                if(row.isFeatured==='yes')
                    is_featured = '<span class="td_qn-featured-post" title="Главная Новость"><i class="fas fa-star"></i></span>';

                html+='<ul class="td_qn-data-content">';
                    html+='<li>'+is_featured+'<span class="td_qn-post-title"><a href="'+row.link+'">'+row.post_title+'</a></span></li>';

                    let categories = '';
                    $.each(row.categories,function(i,v){
                        categories+='<span class="td_qn-category">'+v.cat_name+'</span>';
                    });


                    html+='<li><span class="td_qn-post-date">'+date_format+'</span><span class="td_qn-post-category">'+categories+'</span> <span class="td_qn-post-date"><i class="fas fa-eye"></i>'+row.Views+'</span></li>'
                html+='</ul>';
                return html;
                }
            });
            arrColumns.push({ "data": "post_date", "targets": index++, 'visible':false});
            arrColumns.push({"data":"categories","targets":index++,'visible':false});

            return arrColumns;
        }
        function managedClisk(id)
        {
            let $node = $("#"+id),
                $child = $("#"+$node.data("child"));
            $node.on("click",function(e){
                $child.toggle();
            });

        }

    })(jQuery);
</script>
<a class="on" id="td_qn-cntrl" href="#" data-child="td_qn-overlay">
    <span class="td_qn-show-news-feed">
        <i class="fas fa-anchor"></i>
    </span>
</a>

<div class="td_qn-overlay" id="td_qn-overlay">
    <div class="td_qn-data-container">
        <a class="td_qn-close" id="td_qn-close" data-child="td_qn-overlay">
            <i class="fas fa-times-square"></i>
        </a>
        <div class="td_qn-data">

            <div class="td_qn-filters">
                <ul>
                    <li><label>Filter:</label></li>
                    <li>
                        <?php
                            $categories = get_categories(array(
                                    'hide_empty'=>1,
                                    'orderby'=>'name',
                                    'hierarchical'=>false
                            ));
                        ?>
                        <select id="td_qn-filter-by-category" name="td_qn-filter-by-category">
                                <option value="">Категория</option>
                                <?php foreach ($categories as $c){
                                        echo '<option value="'.$c->term_id.'">'.$c->name.'</option>';
                                 } ?>
                        </select>
                    </li>
                    <li>
                        <input type="datetime" class="datepicker" readonly id="td_qn-filter-start-date" placeholder="от" name="td_qn-filter-start-date" value="" style="width:60px;    border: 1px solid #d7d7d7;     border-radius: 2px;">
                        <input type="datetime" class="datepicker" readonly id="td_qn-filter-end-date" placeholder="до" name="td_qn-filter-end-date" value="" style="width:60px;    border: 1px solid #d7d7d7; border-radius: 2px;">
                        <a href="#" id="td_qn-reset-date-range" class="td_qn-reset-date-range">
                            <i class="fas fa-times"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <table class="td_qn-table" id="td_qn-table">
                <thead>

                <tr>
                    <th style="display: none;">ID</th>
                    <th>Лента Новостей</th>
                    <th style="display: none;">Date</th>
                    <th style="display: none;">Categories</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
