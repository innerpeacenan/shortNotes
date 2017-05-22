/**
 * 这个插件代码量很少，适合进一步学习和查看
 * [autoheight.js used with jQuery](http://kamilkp.github.io/autoheight/examples/jquery.html)
 */
(function($){
  $.fn.autoTextarea = function(options) {
    var defaults={
      maxHeight:null,
      minHeight:$(this).height()
    };
    var opts = $.extend({},defaults,options);
    return $(this).each(function() {
    // $(this).bind("paste cut keydown keyup focus blur",function(){
      $(this).bind("paste focus keydown",function(e){
    	  // console.debug(( e.type==="keydown" && e.which === 13 ) || e.type==="focus" || e.type==="paste");
    	  if ( ( e.type==="keydown" && e.which === 13 ) || e.type==="focus" || e.type==="paste"){//当按下回车键时
    		  var height,style=this.style;
    		  this.style.height = opts.minHeight + 'px';
    		  if (this.scrollHeight > opts.minHeight) {
    			  if (opts.maxHeight && this.scrollHeight > opts.maxHeight) {
    				  height = opts.maxHeight;
    				  style.overflowY = 'scroll';
    			  } else {
    				  height = this.scrollHeight;
    				  style.overflowY = 'hidden';
    			  }
    			  style.height = height + 50 +  'px';
    		  }
    		 
    		 // $("html, body").animate({ scrollTop: $('textarea:focus').eq(0).height() }, 100);
    		  
    	  }
      });
    });
  };
})(jQuery);