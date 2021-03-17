/*!
 * jQuery fileUploader v1.0
 * http://www.cnblogs.com/Zjmainstay
 *
 * Copyright 2012, August 19
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * Author: Zjmainstay
 */
(function($){
	var fileUploader = function() {
		var self 		= this;
		this.options 	= null;
		this._options 	= null;
		this._defaults 	= {
			fileElementId:'inputfile',
			feedbackElementId:'feedback',
			imageSrc:'addfile.jpg',
			imageTitle:'点击添加图片',
			imageAlt:'点击添加图片',
			imageStyle:'cursor:pointer;border: 1px solid #AABBCC;float:left;',
			containerCSS:{},
			loadingSpan:'<span class="loading" style="display:none;background:url(loading.gif) no-repeat;width:16px;height:16px;float:left;margin: 3px;position: relative;top: 10px;"></span>',
			loadingIESpan:'<span class="loading" style="display:none;background:url(loading.gif) no-repeat;width:16px;height:16px;float:left;margin: 2px;position: relative;"></span>',
			baseDir:'./files/',
			processUrl:'uploader.php'
		};
		//初始化选项
		this.initOptions = function(options) {
			self._options = options || {};
			self.options  = $.extend({}, self._defaults, self._options);
			self.options.fileElementId = self.options.fileElementId || 'inputfile';
			self.options.feedbackElementId = self.options.feedbackElementId || 'feedback';
			self._options = null;
			self._defaults = null;
		};
		this.createUploader = function(options){
			this.initOptions(options);
			if($("#"+self.options.fileElementId).length == 0){
				if(typeof(FormData) != 'function'){
					var formHtml = '<div id="addpicContainer">'
						+'<iframe id="uploadIframe" name="uploadIframe" style="display:none;"></iframe>'
						+'<form action="'+self.options.processUrl+'" method="post" target="uploadIframe" name="uploadForm" id="uploadForm" enctype="multipart/form-data">'
						+'<input type="file" id="'+self.options.fileElementId+'" name="upload_file" style="float:left;"/>'
						+'<input type="hidden" name="basedir" value="'+encodeURIComponent(self.options.baseDir)+'"/>'
						+'<input type="hidden" name="isIE" value="1"/></form>'
						+self.options.loadingIESpan+'</div>';
				}else {
					var formHtml = '<div id="addpicContainer">'
					+'<img onclick="getElementById(\''+self.options.fileElementId+'\').click()" class="uploadImageButton" style="'+self.options.imageStyle+'" title="'+self.options.imageTitle+'" alt="'+self.options.imageAlt+'" src="'+self.options.imageSrc+'">'
					+'<input type="file" multiple="multiple" id="'+self.options.fileElementId+'" name="upload_file" style="width:0;height:0"/>'
					+self.options.loadingSpan+'</div>';
				}
				$(formHtml).appendTo($("body")).css(self.options.containerCSS);
			}
			if($("#"+self.options.feedbackElementId).length == 0){
				$("#addpicContainer").after('<div id="'+self.options.feedbackElementId+'" style="float:left;clear:both;margin-top:10px;"></div>');
			}
			$("#uploadIframe").load(function(){
				var data = $(window.frames['uploadIframe'].document.body).find("textarea");
				self.successHandler(data);
			});
			$("#"+self.options.fileElementId).change(function(){
				if(typeof(FormData) != 'function'){
					$("#uploadForm").submit();
				}else {
					var data = new FormData();
					//添加上传文件
					$.each($('#'+self.options.fileElementId)[0].files, function(i, file) {
						data.append('upload_file'+i, file);
					});
					//自定义上传根目录
					data.append('basedir',encodeURIComponent(self.options.baseDir));
					//显示加载中...图片
					$(".loading").show();
					//开始上传
					$.ajax({
						url:self.options.processUrl,
						type:'POST',
						data:data,
						cache: false,
						contentType: false,		//不可缺参数
						processData: false,		//不可缺参数
						success:self.successHandler,
						error:function(){
							alert('上传出错');
							$(".loading").hide();	//加载失败移除加载图片
						}
					});
				}
			});
		}
		this.successHandler = function(data){
			data = $(data).html();
			if(!!data){
				//第一个feedback数据直接append，其他的用before第1个（ .eq(0).before() ）放至最前面。
				//data.replace(/&lt;/g,'<').replace(/&gt;/g,'>') 转换html标签，否则图片无法显示。
				if($("#"+self.options.feedbackElementId).children('div').length == 0) $("#"+self.options.feedbackElementId).append('<div>'+data.replace(/&lt;/g,'<').replace(/&gt;/g,'>')+'</div>');
				else $("#"+self.options.feedbackElementId).children('div').eq(0).before('<div>'+data.replace(/&lt;/g,'<').replace(/&gt;/g,'>')+'</div>');
				$(".loading").hide();	//加载成功移除加载图片
			}
		}
	}
	$.extend({fileUploader: new fileUploader()});
})(jQuery);