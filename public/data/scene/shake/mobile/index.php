<!DOCTYPE html>



<html>



<head>





	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <?php  



if(isset($_GET["wecha_id"])){

	$wecha_id = $_GET["wecha_id"];

	include ('../../config.php');

	$sql=mysql_query("SELECT * FROM `tp_weixin_shake_toshake` where `wecha_id`='$wecha_id'");

	$result=mysql_fetch_row($sql);

		if($result[1] != '') {

			

		}else{

	die("没有查询到您的信息，请返回微信，重新回复！");

}

 

}else{

	die("invild action");

}

 

?>





	<meta name="apple-mobile-web-app-capable" content="yes">



	<meta name="apple-mobile-web-app-status-bar-style" content="black">



	<meta name=​"apple-touch-fullscreen" content=​"yes">



	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1 maximum-scale=1">



    <script src="./shake/jquery.js"></script>



	<style type="text/css">



		body{margin:0;padding:0;background:url(./shake/bg.jpg) repeat center top; text-align:center}



		#dd { width:240px; height:60px; margin:0 auto; margin-top:200px}



		.page { background:url(./shake/b.jpg) no-repeat center top}



		#result { font-size:70px; color:#fff; margin-top:170px;}



	</style>

</head>



<body>



<div class="page">



 <div id = "num2" style="display:none">0</div>



 <div id = "num" style = "width:300px; text-align:center; color:#fff; margin:0px auto;  font-size:70px; padding-top:40px">0</div>



 <div id="result">准备....</div>



 <!--<input type="button" id="dd" value="点击开始摇一摇">-->








</div>



<script type="text/javascript" >



window.onload = function()



{



	document.addEventListener('WeixinJSBridgeReady', function onBridgeReady() {



       WeixinJSBridge.call('hideToolbar');



    });



	var div = document.getElementById("num");



	var div2 = document.getElementById("num2");



	var xray;



	var flag=true;



	var re=0;



	var stype=true;



	var flagr=true;



	var flagl=true;



	var ctime=3*1000;



	var startondevice=true;



	var output="摇吧！";



function ondevice(){



	if (window.DeviceMotionEvent){



	   window.ondevicemotion = function(e){



		 e = e || window.event;



		 xray = e.accelerationIncludingGravity.x>>0;



		 if(stype==true && xray!=0){setType()}    



         if(consider()){



	if(startondevice==false) {

			 div2.innerHTML=parseInt(div2.innerHTML)+1;



			 div.innerHTML=parseInt(parseInt(div2.innerHTML)/2);

	}



			 } 



	   }



	}



	else{



		$(document).mouseover(function(){



	if(startondevice==false) {



			div2.innerHTML=parseInt(div2.innerHTML)+1;



			 div.innerHTML=parseInt(parseInt(div2.innerHTML)/2.2);



			output="敲吧！";

	}



			})



		}

}



	function setType(){



		



	   if(xray>0){re=1;}



		if(xray<0){re=2;}



		stype=false;



		}



    function consider(){



		if(re==1){



			if(xray>0 && flag==true){ flag=false; return true;}



			if(xray<0 && flag==false){ flag=true; return true;}



			}



		if(re==2){



			if(xray<0 && flag==true){ flag=false; return true;}



			if(xray>0 && flag==false){ flag=true; return true;}



			}



		}



  function login(){ 



   



    $.ajax({ 



    type: "get", 



    url : "<?php echo Web_ROOT;?>/shake/commit.php",



    dataType:'text', 



    data: 'point='+parseInt($('#num').html())+'&wecha_id=<?php echo $wecha_id ?>',



    success: function(data){



		kk=data; 



		if(kk==1){



		$('#num').html(0);



        $('#result').html("等待开始") 

		

			div2.innerHTML=0;

		

		startondevice = true;}

		

		else if(kk==3){

			

		$('#num').html("错误");



        $('#result').html("返回重新回复") 

		

			div2.innerHTML=0;

		

		startondevice = true;}



		else{



			 if(startondevice==true){startondevice=false; ondevice();}



			 $('#result').html(output); 



			 }



      } 



	  });



	hoko = setTimeout(login,1000)



    }



  






	  login();



	



  }



  















</script>



</body>



</html>