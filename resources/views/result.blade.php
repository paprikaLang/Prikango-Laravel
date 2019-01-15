
<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml">
{% load staticfiles %}
<head>
<meta http-equiv="X-UA-Compatible" content="IE=emulateIE7" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Prikango搜索引擎</title>
<link href="{% static 'css/style.css' %}" rel="stylesheet" type="text/css" />
<link href="{% static 'css/result.css' %}" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="container">
	<div id="hd" class="ue-clear">
    	<a href="/home"><div class="logo"></div></a>
        <div class="inputArea">
        	<input type="text" class="searchInput" name="keyWords" value="{{ key_words }}"/>
            <input type="button" class="searchButton" onclick="add_search()"/>
        </div>
    </div>
    <div class="nav">
    	<ul class="searchList">
            <li class="searchItem" data-type="article">article</li>
            <li class="searchItem" data-type="job">job</li>
        </ul>
    </div>
	<div id="bd" class="ue-clear">
        <div id="main">
        	<div class="sideBar">
            	
                <div class="subfield">网站</div>
                <ul class="subfieldContext">
                	<li>
                    	<span class="name">JOBBOLE</span>
						<span class="unit">({{ jobbole_count }})</span>
                    </li>
                    <li>
                    	<span class="name">LAGOU</span>
						<span class="unit">({{ lagou_count }})</span>
                    </li>
                    <li class="more">
                    	<a href="javascript:;">
                        	<span class="text">更多</span>
                        	<i class="moreIcon"></i>
                        </a>
                    </li>
                </ul>
                
                            
                <div class="sideBarShowHide">
                	<a href="javascript:;" class="icon"></a>
                </div>
            </div>
            <div class="resultArea">
            	<p class="resultTotal">
                	<span class="info">找到约&nbsp;<span class="totalResult">{{ total_nums }}</span>&nbsp;条结果(用时<span class="time">{{ last_seconds }}</span>秒)，共约<span class="totalPage">{{ page_nums }}</span>页</span>
                </p>
                <div class="resultList">
                    {% for hit in all_hits %}
                    <div class="resultItem">
                            <div class="itemHead">
                                <a href="{{ hit.url }}"  target="_blank" class="title">{% autoescape off %}{{ hit.title }}{% endautoescape %}</a>
                                <span class="divsion">-</span>
                                <span class="fileType">
                                    <span class="label">来源：</span>
                                    <span class="value">{{ s_type }}</span>
                                </span>
                                <span class="dependValue">
                                    <span class="label">得分：</span>
                                    <span class="value">{{ hit.score }}</span>
                                </span>
                            </div>
                            <div class="itemBody">
                                {% autoescape off %}{{ hit.content }}{% endautoescape %}
                            </div>
                            <div class="itemFoot" style="margin-bottom: 30px;">
                                <span class="info">
                                    <label>网站：</label>
                                    <span class="value">{{ s_type }}</span>
                                </span>
                                <span class="info">
                                    <label>发布时间：</label>
                                    <span class="value">{{ hit.create_date }}</span>
                                </span>
                            </div>
                        </div>
                    {% endfor %}
                </div>
                <!-- 分页 -->
                <div class="pagination ue-clear"></div>
                <!-- 相关搜索 -->


            </div>
            <div class="historyArea">
            	<div class="hotSearch">
                	<h6>热门搜索</h6>
                    <ul class="historyList">
                        {% for search_word in top_search %}
                            <li><a href="/search?q={{ search_word }}">{{ search_word }}</a></li>
                        {% endfor %}
                    </ul>
                </div>
                <div class="mySearch">
                	<h6>我的搜索</h6>
                    <ul class="historyList">

                    </ul>
                </div>
            </div>
        </div><!-- End of main -->
    </div><!--End of bd-->
</div>
</body>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="../js/global.js"></script>
<script type="text/javascript" src="../js/pagination.js"></script>
<script type="text/javascript">
    var search_url = "{% url 'search' %}"
    var s_type = "{{ s_type }}"
	$('.searchList').on('click', '.searchItem', function(){
        if (s_type == 'article') {
            s_type = 'job'
        }else {
            s_type = 'article'
        }
		$('.searchList .searchItem').removeClass('current');
		$(this).addClass('current');
		add_search()
	});
	
	$.each($('.subfieldContext'), function(i, item){
		$(this).find('li:gt(2)').hide().end().find('li:last').show();		
	});

	$('.subfieldContext .more').click(function(e){
		var $more = $(this).parent('.subfieldContext').find('.more');
		if($more.hasClass('show')){
			
			if($(this).hasClass('define')){
				$(this).parent('.subfieldContext').find('.more').removeClass('show').find('.text').text('自定义');
			}else{
				$(this).parent('.subfieldContext').find('.more').removeClass('show').find('.text').text('更多');	
			}
			$(this).parent('.subfieldContext').find('li:gt(2)').hide().end().find('li:last').show();
	    }else{
			$(this).parent('.subfieldContext').find('.more').addClass('show').find('.text').text('收起');
			$(this).parent('.subfieldContext').find('li:gt(2)').show();	
		}
		
	});
	
	$('.sideBarShowHide a').click(function(e) {
		if($('#main').hasClass('sideBarHide')){
			$('#main').removeClass('sideBarHide');
			$('#container').removeClass('sideBarHide');
		}else{
			$('#main').addClass('sideBarHide');	
			$('#container').addClass('sideBarHide');
		}
        
    });
    var key_words = $("input[name='keyWords']").val()
	//分页
    var per_page = 10
    if (s_type == 'job'){
        per_page = 15
    }
	$(".pagination").pagination({{ total_nums }}, {
		current_page :{{ page|add:'-1' }}, //当前页码
		items_per_page :per_page,
		display_msg :true,
		callback :(page_id, jq) => {
            page_id += 1
            window.location.href=search_url+'?q='+key_words+'&p='+page_id+'&s_type='+s_type
        }
	});
	
	{#setHeight();#}
	$(window).resize(function(){
		setHeight();	
	});
	
	function setHeight(){
		if($('#container').outerHeight() < $(window).height()){
			$('#container').height($(window).height()-33);
		}	
	}
</script>
<script type="text/javascript">
    $('.searchList').on('click', '.searchItem', function(){

        $('.searchList .searchItem').removeClass('current');
        $(this).addClass('current');
        add_search()

    });

    $('.searchInput').on('focus', function(){
        $('.dataList').show()
    });

    $('.dataList').on('click', 'li', function(){
        var text = $(this).text();
        $('.searchInput').val(text);
        $('.dataList').hide()
    });

    hideElement($('.dataList'), $('.searchInput'));
</script>
<script>
    var search_url = "{% url 'search' %}"
    var searchArr;
    //定义一个search的，判断浏览器有无数据存储（搜索历史）
    if(localStorage.search){
        //如果有，转换成 数组的形式存放到searchArr的数组里（localStorage以字符串的形式存储，所以要把它转换成数组的形式）
        searchArr= localStorage.search.split(",")
    }else{
        //如果没有，则定义searchArr为一个空的数组
        searchArr = [];
    }
    //把存储的数据显示出来作为搜索历史
    MapSearchArr();

    function add_search(){
        var val = $(".searchInput").val();
        if (val.length>=2){
            //点击搜索按钮时，去重
            FilterRepeat(val);
            //去重后把数组存储到浏览器localStorage
            localStorage.search = searchArr;
            //然后再把搜索内容显示出来
            MapSearchArr();
        }

        window.location.href=search_url+'?q='+val+"&s_type="+$(".searchItem.current").attr('data-type')

    }

    function MapSearchArr(){
        var tmpHtml = "";
        var arrLen = 0
        if (searchArr.length > 6){
            arrLen = 6
        }else {
            arrLen = searchArr.length
        }
        for (var i=0;i<arrLen;i++){
            tmpHtml += '<li><a href="/search?q='+searchArr[i]+'">'+searchArr[i]+'</a></li>'
        }
        $(".mySearch .historyList").append(tmpHtml);
    }
    function removeByValue(arr, val) {
      for(var i=0; i<arr.length; i++) {
        if(arr[i] == val) {
          arr.splice(i, 1);
          break;
        }
      }
    }
    //去重
    function FilterRepeat(val){
        var kill = 0;
        for (var i=0;i<searchArr.length;i++){
            if(val===searchArr[i]){
                kill ++;
            }
        }
        if(kill<1){
            searchArr.unshift(val);
        }else {
            removeByValue(searchArr, val)
            searchArr.unshift(val)
        }
    }
</script>
</html>