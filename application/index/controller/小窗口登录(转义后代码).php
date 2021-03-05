<div class="login-wrap"> 
   <div class="login-form"> 
     <div class="coagent"> 
        <div class="tit">
         <h3>用第三方账号直接登录</h3>
         <span></span>
        </div> 
     <div class="coagent-warp"> 
          <a href="user.php?act=oath&amp;type=qq&amp;user_callblock=flow.php" class="qq"><b class="third-party-icon qq-icon"></b></a> 
     </div> 
  </div> 
  <div class="login-box"> 
     <div class="tit">
          <h3>账号登录</h3>
          <span></span>
     </div> 
  <div class="msg-wrap"></div> 
      <div class="form"> 
        <form name="formLogin" action="user.php" method="post" onsubmit="userLogin();return false;"> 
        <div class="item"> 
            <div class="item-info"> 
               <i class="iconfont icon-name"></i> 
               <input type="text" id="loginname" name="username" class="text" value="" placeholder="用户名/邮箱/手机" /> 
            </div> 
       </div> 
       <div class="item"> 
            <div class="item-info"> 
               <i class="iconfont icon-password"></i> 
               <input type="password" style="display:none" /> 
               <input type="password" id="nloginpwd" name="password" value="" class="text" placeholder="密码" /> 
            </div> 
       </div> 
       <div class="item"> 
              <input id="remember" name="remember" type="checkbox" class="ui-checkbox" /> 
              <label for="remember" class="ui-label">请保存我这次的登录信息。</label> 
       </div> 
       <div class="item item-button"> 
              <input type="hidden" name="dsc_token" value="88feda8561dc803c2ab8237c3b465fe4" /> 
              <input type="hidden" name="act" value="act_login" /> 
              <input type="hidden" name="back_act" value="flow.php" /> 
              <input type="submit" name="submit" value="登&nbsp;&nbsp;录" class="btn sc-redBg-btn" /> 
       </div> 
       <div class="lie"> 
              <a href="user.php?act=get_password" class="notpwd gary fl" target="_blank">忘记密码？</a> 
              <a href="user.php?act=register" class="notpwd red fr" target="_blank">免费注册</a> 
       </div> 
       </form> 
      </div> 
    </div> 
  </div> 
</div>

<script type='text/javascript'>
var username_empty = '<i></i>请输入用户名';
var username_shorter = '<i></i>用户名长度不能少于 4 个字符。';
var username_invalid = '<i></i>用户名只能是由字母数字以及下划线组成。';
var password_empty = '<i></i>请输入密码';
var password_shorter = '<i></i>登录密码不能少于 6 个字符。';
var confirm_password_invalid = '<i></i>两次输入密码不一致';
var captcha_empty = '<i></i>请输入验证码';
var email_empty = '<i></i>Email 为空';
var email_invalid = '<i></i>Email 不是合法的地址';
var agreement = '<i></i>您没有接受协议';
var msn_invalid = '<i></i>msn地址不是一个有效的邮件地址';
var qq_invalid = '<i></i>QQ号码不是一个有效的号码';
var home_phone_invalid = '<i></i>家庭电话不是一个有效号码';
var office_phone_invalid = '<i></i>办公电话不是一个有效号码';
var mobile_phone_invalid = '<i></i>手机号码不是一个有效号码';
var msg_un_blank = '<i></i>用户名不能为空';
var msg_un_length = '<i></i>用户名最长不得超过15个字符，一个汉字等于2个字符';
var msg_un_format = '<i></i>用户名含有非法字符';
var msg_un_registered = '<i></i>用户名已经存在,请重新输入';
var msg_can_rg = '<i></i>可以注册';
var msg_email_blank = '<i></i>邮件地址不能为空';
var msg_email_registered = '<i></i>邮箱已存在,请重新输入';
var msg_email_format = '<i></i>格式错误，请输入正确的邮箱地址';
var msg_blank = '<i></i>不能为空';
var no_select_question = '<i></i>您没有完成密码提示问题的操作';
var passwd_balnk = '<i></i>密码中不能包含空格';
var msg_phone_blank = '<i></i>手机号码不能为空';
var msg_phone_registered = '<i></i>手机已存在,请重新输入';
var msg_phone_invalid = '<i></i>无效的手机号码';
var msg_phone_not_correct = '<i></i>手机号码不正确，请重新输入';
var msg_mobile_code_blank = '<i></i>手机验证码不能为空';
var msg_mobile_code_not_correct = '<i></i>手机验证码不正确';
var msg_confirm_pwd_blank = '<i></i>确认密码不能为空';
var msg_identifying_code = '<i></i>验证码不能为空';
var msg_identifying_not_correct = '<i></i>验证码不正确';
/* * * 会员登录 */
function userLogin() {
    var frm = $('form[name='formLogin ']');
    var username = frm.find('input[name='username ']');
    var password = frm.find('input[name='password ']');
    var captcha = frm.find('input[name='captcha ']');
    var dsc_token = frm.find('input[name='dsc_token ']');
    var error = frm.find('.msg-error');
    var msg = '';
    if (username.val() == '') {
        error.show();
        username.parents('.item').addClass('item-error');
        msg += username_empty;
        showMesInfo(msg);
        return false;
    }
    if (password.val() == '') {
        error.show();
        password.parents('.item').addClass('item-error');
        msg += password_empty;
        showMesInfo(msg);
        return false;
    }
    if (captcha.val() == '') {
        error.show();
        captcha.parents('.item').addClass('item-error');
        msg += captcha_empty;
        showMesInfo(msg);
        return false;
    }
    var back_act = frm.find('input[name='back_act ']').val();
    Ajax.call('/Tpshop/index.php/member/account/login.html', 'username=' + username.val() + '&password=' + password.val() + '&dsc_token=' + dsc_token.val() + '&captcha=' + captcha.val() + '&back_act=' + back_act, return_login, 'POST', 'JSON');
}
function return_login(result) {
    if (result.error > 0) {
        showMesInfo(result.message);
    } else {
        if (result.ucdata) {
            $('body').append(result.ucdata)
        }
        location.href = result.url;
    }
}
function showMesInfo(msg) {
    $('.login-wrap .msg-wrap').empty();
    var info = '<div class='msg - error '><b></b>' + msg + '</div>';
    $('.login-wrap .msg-wrap').append(info);
} 
</script>