
/*
    登录注册页面使用
*/
            function btn_register(a){
                $("#register-form")[0].style.display="block";
                $("#login-form")[0].style.display="none";
                $(".i-tab-active").removeClass('i-tab-active');
                $(a).addClass('i-tab-active');
            }
            function btn_login(a){
                $("#login-form")[0].style.display="block";
                $("#register-form")[0].style.display="none";
                $(".i-tab-active").removeClass('i-tab-active');
                $(a).addClass('i-tab-active');
            }
            function cancel(a){
                $(a).removeClass('error');
                $(a)[0].nextElementSibling.innerHTML="";
            }
            function login() {
                var data = {};
	            var t = $('#login-form').serializeArray();
	            $.each(t, function() {
	                 data[this.name] = this.value;
	            });
                $.post("php/user.php?type=login",data,function(data){
                    //提交注册信息，返回json信息，根据json信息判断注册是否成功。失败则显示json的失败信息，成功则提示和跳转。
                    var obj = $.parseJSON(data);
                    if(obj.status==1){
                        alert(obj.message);
                        window.location.href=('./');
                    }else if(obj.status==2){
                        alert(obj.message);
                        window.location.reload();
                    }else{
                        $(obj.id)[0].parentNode.setAttribute("onclick","cancel(this);");
                        $(obj.id)[0].parentNode.classList.add("error");
	                    $(obj.id)[0].parentNode.nextElementSibling.innerHTML=obj.message;
                    }
	            });
	            return false;
            }
            function register() {
                var data = {};
	            var t = $('#register-form').serializeArray();
	            $.each(t, function() {
	                 data[this.name] = this.value;
	            });
                $.post("php/user.php?type=register",data,function(data){
                    //提交注册信息，返回json信息，根据json信息判断注册是否成功。失败则显示json的失败信息，成功则提示和跳转。
                    var obj = $.parseJSON(data);
                    if(obj.status==1){
                        alert(obj.message);
                        window.location.href=('./login.html');
                    }else if(obj.status==2){
                        alert(obj.message);
                        window.location.reload();
                    }else{
                        $(obj.id)[0].parentNode.setAttribute("onclick","cancel(this);");
                        $(obj.id)[0].parentNode.classList.add("error");
	                    $(obj.id)[0].parentNode.nextElementSibling.innerHTML=obj.message;
                    }
	            });
	            return false;
            }
            // function post_code(){
            //     $.post("php/post.php?type=code","",function(data){
            //         //请求发送验证码，并改变获取验证码按钮
	           //    // document.write(data);
	           //     console.log(data);
	           // });
            // }


/*
    角色添加页面使用
*/
            function change_img(a){
                $("input[name='img']").each(function() {//全部取消选中
                    $(this).removeAttr("checked");
                    $(this)[0].parentNode.setAttribute("style","");
                });
                $(a).find("input").prop("checked", "checked");
                $(a).attr("style","border: 2px #9f8c8c solid;");
                // console.log($(a).find("input").val());
                $("#pre_view").find("img").attr('src','images/img/'+$(a).find("input").val());
            }
            
            function change_color(a){
                $("#pre_view").attr('style','background:'+$(a).val()+';');
            }
            
            function post() {
                var data = {};
	            var t = $('#post-form').serializeArray();
	            $.each(t, function() {
	                 data[this.name] = this.value;
	            });
                $.post("php/post.php?type=add_role",data,function(data){
                    //提交注册信息，返回json信息，根据json信息判断注册是否成功。失败则显示json的失败信息，成功则提示和跳转。
                    var obj = $.parseJSON(data);
                    if(obj.status==1){
                        alert(obj.message);
                        window.location.href=('./');
                    }else if(obj.status==2){
                        alert(obj.message);
                        display_view(obj.message);
                        window.location.reload();
                    }else{
                        $(obj.id)[0].parentNode.setAttribute("onclick","cancel(this);");
                        $(obj.id)[0].parentNode.classList.add("error");
	                    $(obj.id)[0].parentNode.nextElementSibling.innerHTML=obj.message;
                    }
	               // document.write(data);
	               // console.log(data);
	            });
	            return false;
            }


/*
    主页面使用
*/
            function display_view(tt){//用于显示提示信息
                $("#foot_tip")[0].innerHTML=tt;
                setTimeout(function(){$("#foot_tip")[0].innerHTML="";},5000);
            }
            
            function init(){//获取用户信息和角色信息
            
                $.post("php/get.php?type=get_user","",function(data){
                    var base = new Base64();
                    var obj = $.parseJSON(data);
                    if( obj.status === "0" ){
                        alert(obj.message);
                        display_view(obj.message);
                        window.location.href=(obj.url);
                        // document.write(obj.message);
                    }else if(obj.status==2){
                        alert(obj.message);
                        display_view(obj.message);
                        window.location.reload();
                    }else{
                        $("#header_title")[0].innerHTML=obj.name+" 的智能鞋柜 >>>";
                        $("#user_info")[0].innerHTML=obj.roleNumber+"个角色  "+obj.cabNumber+"个鞋柜  剩余"+obj.cabLeftNumber+"个鞋柜";
                        $("#button")[0].innerHTML=base.decode(obj.message);
                    }
	            });
            }
            
            function show_shoes(a){//用于显示对应角色的对应鞋柜信息，并控制用户角色图标显示突出的变化
                
                var role_id = $(a).attr("data-id");
	            //获取鞋柜信息
                $.post("php/get.php?type=get_shoes","role_id="+role_id,function(data){
                    var obj = $.parseJSON(data);
                    var base = new Base64();
                    if( obj.status === "0" ){
                        alert(obj.message);
                        display_view(obj.message);
                        // window.location.href=(obj.url);
                        // document.write(obj.message);
                    }else if(obj.status==2){
                        alert(obj.message);
                        display_view(obj.message);
                        window.location.reload();
                    }else{
                        $("#foot")[0].innerHTML=base.decode(obj.message);
                        
                        $('#active').attr("onclick","show_shoes(this);");
                        if($('#active').length>0)
                        $('#active')[0].children[0].style.display="";
                        $('#active').attr("id","");
                        $(a).attr("id","active");
                        $(a).attr("onclick","hide_shoes(this);");
                        $(a)[0].children[0].style.display="block";
                    }
	            });
                
            }
            
            function hide_shoes(a){//取消用户角色图标显示突出的变化
                $(a).attr("id","");
                $(a).attr("onclick","show_shoes(this);");
                $(a)[0].children[0].style.display="";
            }

            function delete_role(i,id){
                if(confirm('确认删除该角色吗！')){
                    //预置使用ajax删除
                    $.post("php/post.php?type=delete_role","role_id="+id,function(data){
                        var obj = $.parseJSON(data);
                        if( obj.status === "0" ){
                            alert(obj.message);
                            display_view(obj.message);
                            // window.location.href=(obj.url);
                            // document.write(obj.message);
                        }else if(obj.status==2){
                            alert(obj.message);
                            display_view(obj.message);
                            window.location.reload();
                        }else{
                            $("#foot")[0].innerHTML='<h1 id="foot_tip" style="">请选择角色</h1>';
                            
                            $(i)[0].parentElement.outerHTML="";
                            alert(obj.message);
                            init();
                        }
	                });
                }
                else{
                    alert('删除操作已取消！');
                }
            }
            
            function delete_foot(i,id,role_id){
                if(confirm('确认取出该鞋吗！')){
                    //预置使用ajax删除
                    $.post("php/post.php?type=get_shoes","shoes_id="+id,function(data){
                        var obj = $.parseJSON(data);
                        if( obj.status === "0" ){
                            alert(obj.message);
                            display_view(obj.message);
                        }else if(obj.status==2){
                            alert(obj.message);
                            display_view(obj.message);
                            window.location.reload();
                        }else{
                            display_view(obj.message);
                            $(i)[0].parentElement.outerHTML="";
                            alert(obj.message);
                            init();
                            //重新获取鞋柜信息
                            $.post("php/get.php?type=get_shoes","role_id="+role_id,function(data){
                                var obj = $.parseJSON(data);
                                var base = new Base64();
                                if( obj.status === "0" ){
                                    alert(obj.message);
                                    display_view(obj.message);
                                }else if(obj.status==2){
                                    alert(obj.message);
                                    display_view(obj.message);
                                    window.location.reload();
                                }else{
                                    $("#foot")[0].innerHTML=base.decode(obj.message);
                                }
	                        });
                        }
	                });
                }
                else{
                    alert('取鞋操作已取消！');
                }
            }
            
            function put_foot(i,id){
                if(confirm('确认放鞋吗！')){
                    //预置使用ajax删除
                    $.post("php/post.php?type=put_shoes","role_id="+id,function(data){
                        var obj = $.parseJSON(data);
                        if( obj.status === "0" ){
                            alert(obj.message);
                            display_view(obj.message);
                        }else if(obj.status==2){
                            alert(obj.message);
                            display_view(obj.message);
                            window.location.reload();
                        }else{
                            display_view(obj.message);
                            $(i)[0].parentElement.outerHTML="";
                            alert(obj.message);
                            init();
                            //重新获取鞋柜信息
                            $.post("php/get.php?type=get_shoes","role_id="+id,function(data){
                                var obj = $.parseJSON(data);
                                var base = new Base64();
                                if( obj.status === "0" ){
                                    alert(obj.message);
                                    display_view(obj.message);
                                }else if(obj.status==2){
                                    alert(obj.message);
                                    display_view(obj.message);
                                    window.location.reload();
                                }else{
                                    $("#foot")[0].innerHTML=base.decode(obj.message);
                                }
	                        });
                        }
	                });
                }
                else{
                    alert('放鞋操作已取消！');
                }
            }

