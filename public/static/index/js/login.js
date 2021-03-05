//加载函数：实现实时查看登录状态
$(function(){
    $.ajax({
        type: 'GET',
        url: login_url,
        dataType: 'json',
        success:function(data){
            if(data.error == 0){
                var html ="<span>您好&nbsp;<a href='#'>"+data.username+"</a></span><span>,欢迎来到&nbsp;&nbsp;<a href=''>"+shop_name+"&nbsp;&nbsp;</a></span><span>[<a href='#' onclick='ajax_logout()'>退出</a>]</span>";
                $('#ECS_MEMBERZONE').html(html);
            }else{
                var html = "<a href='"+login+"' class='link-login red'>请登录</a><a href='"+register+"' class='link-regist'>免费注册</a>";
                $('#ECS_MEMBERZONE').html(html);
            }
        }
    });
    cartGoodsNum();
});

//ajax用户退出登录
function ajax_logout(){
    $.ajax({
        type: 'GET',
        url: logout_url,
        dataType: 'json',
        success:function(data){
           if(data.error == 0){ 
            var html = "<a href='"+login+"' class='link-login red'>请登录</a><a href='"+register+"' class='link-regist'>免费注册</a>";
            $('#ECS_MEMBERZONE').html(html);
            }
        }
    });
}

//头部购物车导航，显示商品数量
     function cartGoodsNum(){
        $.ajax({
            type: "POST",
            url: cart_goods_num,
            dataType: "json",
            success:function(data){
                $("#cart_goods_num").html(data.cartGoodsNum);
            }
        });
    }
