								商城数据表

文章表  article
id  title  keywords  author   link_url   thumb   content   show_top  show_status   cate_id   addtime


文章分类表   cate
id 
cate_name
cate_type   分类类型： 1：系统分类 2：帮助分类 3：网店帮助 4:网店信息 5：普通分类

keywords  description   show_nav   allow_son  sort   pid

配置表  conf
id
ename      sitename
cname      站点名称
form_type  表单类型
conf_type  配置类型
values     可选项
value      默认值


商品分类表 category
id  cate_name   cate_img  (可不要)   sort   pid

商品类型表  type
id type_name

商品属性表   attr
id attr_name attr_type attr_values type_id

商品表 	goods
id goods_name goods_code og_thumb sm_thumb mid_thumb big_thumb markte_price shop_price 

 on_sale category_id  brand_id type_id goods_desc goods_weight goods_unit


会员级别  member_level
id  level_name bom_point top_point rate

会员价格   member_price
id mprice  mlevel_id  goods_id 

商品相册  goods_photo
id goods_id  sm_photo mid_photo big_photo

库存表   product
id goods_id goods_number goods_attr

前台头部导航栏表  nav

前台推荐位 rec_pos
id rec_name rec_type

商品、分类 与 推荐关联表  rec_item
recpos_id value_id value_type

分类项 分类推荐  category_rec

分类项 品牌推荐  brand_rec

大分类推荐 左侧广告图展示位 category_adv
id img_src position link_url category_id

用户主表 user 
id username password email mobile_phone register_type points register_time
													  会员积分(登录时计算会员等级)



 订单表   order
 id out_trade_no(order_num) user_id goods_total_price order_total_price payment distribution order_status 
  																				配送方式
 pay_status post_status post_spent


 订单商品表    order_goods
 id goods_id goods_name goods_market_price goods_shop_price goods_attr_id goods_attr_str 

 goods_num order_id 

 address  订单地址表
 id  user_id name mobile_phone country province city district address zipcode


