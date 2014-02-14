<div id="header_right_1_5">
    <i id="_hRequestBell" class="fa fa-bell-o <?php if($requests_count)
{
   ?>bell<?php } ?>"></i>
    <span title="Новые заявки на размещение рекламы"><a href="/twitter/tweets/request" id="_hRequestsCount"><?php echo $requests_count; ?></a></span>
</div>
<div id="header_l"></div>
<script type="text/javascript">
    setInterval(function() {
        _ajax({
            url: '/ajax/bell',
            type: 'GET',
            dataType: 'json',
            success: function(r)
            {
                if (r.code == 200)
                {
                    if (r.count > 0)
                        $('#_hRequestBell').addClass('bell');
                    else
                        $('#_hRequestBell').removeClass('bell');

                    $('#_hRequestsCount').html(r.count);
                }
            }
        });
    }, 20000);
</script> 
