<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<?php
$location="localhost";		//location of your database
$username="root";			//username for your database
$password="toor";			//passowrd
$database="krn";			//table name
$bgImages = array(array("images/bg.jpg", 1024, 768));

mysql_connect($location,$username,$password);
@mysql_select_db($database) or die( "Unable to select database");

if($_GET)
{
	$language = mysql_real_escape_string($_GET['lang']);
}else
{
	$language = 'en';
}


$menu_id_query = mysql_query("SELECT * FROM menu WHERE language='" . $language . "' ORDER BY id");
$menu_rows = mysql_num_rows($menu_id_query);
?>
<html>
<head>
	<title>Krn</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
	<link rel="shortcut icon" href="../favicon.ico" type="image/x-icon"/>
	<link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
	<link rel="stylesheet" href="css/bgstretcher.css" type="text/css" media="screen"/>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript" src="js/slowEach.js"></script>
	<script type="text/javascript" src="js/bgstretcher.js"></script>
	<script type="text/javascript">
		var transition_speed = 250;
		var width = 140;
		var height = 600;
		var border = 2;
		var numItems = <?php echo $menu_rows; ?>;
		var title_height = 200;
		$(function() {
			//change these variables

			$(document).bgStretcher({
				images: <?php $randomElement = mt_rand(0, count($bgImages) - 1); echo "['" . $bgImages[$randomElement][0] . "'], imageWidth: " . $bgImages[$randomElement][1] . ", imageHeight:" . $bgImages[$randomElement][2]; ?>
			});

			$('#footer_pannel').css('width', (width + border) * numItems + 'px');
			$('.cc_menu').css('width', (width + border) * numItems + 'px');
			$('.cc_menu').css('height', height + 'px');
			$('.cc_menu').css('top', 20+ 'px');
			$('.cc_item').css('width', width + 'px');
			$('.cc_item').css('height', height + 'px');
			$('.cc_item').css('border-right', border + 'px solid black');
			$('.cc_submenu ul').css('width', width + 'px');
			$('.cc_item img').css('width', width + 'px');
			$('.cc_item img').css('height', height + 'px');
			$('.cc_item img').css('top', -height + 'px');
			$('.cc_content').css('width', ((width + border) * numItems - width)+ 'px');
			$('.cc_content').css('height', (height - 20) + 'px');
			$('.cc_content').css('left', -(width * numItems) + 'px');
			title_height = $('#title').css('height');
			$('#cc_back').css('top', (parseInt(title_height.substring(0, title_height.length - 2)) + 30) + 'px');

			var $items 		= $('#cc_menu .cc_item');
			var cnt_items	= $items.length;
			var folded		= false;
			var menu_time;

			$items.find("img").bind('click',function(){
				var $firstLink = $(this).parent().find('.cc_submenu ul > li:first');
				if(folded){
					hideContent();
					showContent($firstLink.attr('class'));
				}	
				else
					fold($firstLink);
			});
			$items.find(".cc_title").bind('click',function(){
				var $firstLink = $(this).parent().find('.cc_submenu ul > li:first');
				if(folded){
					hideContent();
					showContent($firstLink.attr('class'));
				}	
				else
					fold($firstLink);
			});
			$items.unbind('mouseenter')
				  .bind('mouseenter',m_enter)
				  .unbind('mouseleave')
				  .bind('mouseleave',m_leave)
				  .find('.cc_submenu > ul > li')
				  .bind('click',function(){
				var $li_e = $(this);

				if(folded){
					hideContent();
					showContent($li_e.attr('class'));
				}	
				else
					fold($li_e);
			});
			// uncomment this next lines if you dont want to have intro effect
			$('#cc_menu .cc_item').slowEach(transition_speed/numItems, function(index, domElement){
					$(domElement).find('img').stop().animate({'top':'0px'},transition_speed);
					$(domElement).find('.cc_submenu > ul').stop().animate({'height':'200px'},transition_speed);
			})
			//comment out this function if you dont want hover effect
			function m_enter(){
				var $this = $(this);
				clearTimeout(menu_time);
				menu_time 	= setTimeout(function(){
				$this.find('img').stop().animate({'top':'0px'},transition_speed);
				$this.find('.cc_submenu > ul').stop().animate({'height':'200px'},transition_speed);
				},transition_speed / 2);
			}
			
			//comment out this function if you dont want hover effect
			function m_leave(){
				var $this = $(this);
				clearTimeout(menu_time);
				$this.find('img').stop().animate({'top':'-' + height + 'px'},transition_speed);
				$this.find('.cc_submenu > ul').stop().animate({'height':'0px'},transition_speed);
			}
			
			$('#cc_back').bind('click',unfold);
			$('#title').bind('click',unfold);
			
			function fold($li_e){
				var $item		= $li_e.closest('.cc_item');

				var d = 100;
				var step = 0;
				$('#cc_back').animate({
					// 'top': (parseInt(title_height.substring(0, title_height.length - 2)) - 20) + 'px'
					'top': (stripInt($('.cc_menu').css('top')) + stripInt(title_height) - 50) + 'px'
				}, transition_speed);
				$('#cc_back').fadeIn(transition_speed);
				$items.unbind('mouseenter mouseleave');
				$items.not($item).each(function(){
					var $item = $(this);
					$item.stop().animate({
						'marginLeft':'-' + (width + border) + 'px'
					},d += (transition_speed / 2),function(){
						++step;
						if(step == cnt_items-1){
							folded = true;
							showContent($li_e.attr('class'));
						}	
					});
				});
			}
			
			function unfold(){
				$('#cc_content').stop().animate({'left':'-' + (numItems * (width + border)) + 'px'},(transition_speed * 3 / 2),function(){
				$('#cc_back').animate({
					// 'top': (parseInt(title_height.substring(0, title_height.length - 2)) + 20) + 'px'
					'top': (stripInt($('.cc_menu').css('top')) + stripInt(title_height)) + 'px'
				}, transition_speed);
				$('#cc_back').fadeOut(transition_speed);
					var d = 100;
					var step = 0;
				$items.each(function(){
						var $item = $(this);
						
						$item.find('img')
							 .stop()
							 .animate({'top':'-' + height + 'px'},transition_speed)
							 .andSelf()
							 .find('.cc_submenu > ul')
							 .stop()
							 .animate({'height':'0px'},transition_speed);
							 
						$item.stop().animate({
						'marginLeft':'0px'
						},d += (transition_speed / 2),function(){
							++step;
							if(step == cnt_items-1){
								folded = false;
								$items.unbind('mouseenter')
									  .bind('mouseenter',m_enter)
									  .unbind('mouseleave')
									  .bind('mouseleave',m_leave);
								
								hideContent();
							}		  
						});
					});
				});
			}
			
			function showContent(idx){
				$('#cc_content').stop().animate({'left': width + 'px'},(transition_speed / 2),function(){
					$(this).find('.'+idx).fadeIn();
				});
			}
			
			function hideContent(){
				$('#cc_content').find('div').hide();
			}
		});
		$(window).resize(function(){
			var title_height2 = stripInt(title_height);
			if ((($(window).height()/2) - (height / 2) - title_height2) > 0)
			{
				$('#main').css('height', $(window).height() + 'px');
				$('.cc_menu').css('top', (($(window).height() / 2) - (height / 2) - title_height2) + 'px');
				$('#cc_back').css('top', (stripInt($('.cc_menu').css('top')) + title_height2) + 'px');
			}
		})
		function stripInt(i)
		{
			return parseInt(i.substring(0, i.length - 2));
		}
	</script>
</head>
<body>
	<div id="page">
		<div id="main">
			<div id="title"><img src="images/logo.png" /></div>
			<span id="cc_back" class="cc_back"><?php if ($language == 'si') {echo "nazaj";}else{echo "back";}?></span>
			<div id="cc_menu" class="cc_menu">
				<?php 
				$i = 0;
				$j = 0;
				$contentsArray = array();
				while ($row = mysql_fetch_array($menu_id_query)) 
				{
					$i ++;
					$menu_id = $row['id'];
					$content_query = mysql_query("SELECT * FROM contents WHERE id_menu='" . $menu_id . "' ORDER BY id");

					echo '<div class="cc_item" style="z-index:' . ($menu_rows - $i + 1) . ';">';
					echo '<img src="images/' . $i . '.jpg" />';
					echo '<span class="cc_title">' . $row['category'] . '</span>';
					echo '<div class="cc_submenu">';
					echo '<ul>';
					while ($contentRow = mysql_fetch_array($content_query)) 
					{
						$j ++;
						$contentTitle = $contentRow['title'];
						array_push($contentsArray, array($contentRow['title'], $contentRow['text']));
						echo '<li class="cc_content_' . $j . '">' . $contentRow['title'] . '</li>';
					}
					echo '</ul>';
					echo '</div>';
					echo '</div>';
				}
				?>
				<div id="cc_content" class="cc_content">
				<?php 
				for ($k = 0; $k < count($contentsArray); $k ++)
				{
					echo '<div class="cc_content_' . ($k + 1). '">';
					echo '<h1>' . $contentsArray[$k][0] . '</h1>';
					echo '<p>' . $contentsArray[$k][1] . '</p>';
					echo '</div>';
				}
				?>
				</div>
			</div>
			<div id="footer_pannel">
				<div id="language">
					<a href="?lang=si"><img src="images/si.png" alt="SI" /></a>
					<a href="?lang=en"><img src="images/gb.png" alt="ENG" /></a>
				</div>
				<div id="copyright">COPYLEFT</div>
				<div id="author">AUTH</div>
			</div>
		</div>
	</div>
</body>
</html>
