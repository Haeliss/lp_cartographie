<?php session_start(); 
$nbDonut = $_SESSION["nbDonut"];
if(isset($_POST['nbDonut'])) {
	$_SESSION["nbDonut"] += 1;
	header('Location: index.php');
	exit();
}
?>
<html>
  <head>
    <script src="https://aframe.io/releases/0.9.0/aframe.min.js"></script>
     <script src="https://unpkg.com/aframe-environment-component@1.1.0/dist/aframe-environment-component.min.js"></script>
	 <title>Take donut before Marge !</title>
  </head>
  <!-- <body> -->
  <body>
  <script>
	var nbDonut = <?php echo $_SESSION['nbDonut']; ?>;
	AFRAME.registerComponent('cursor-listener', {
    init: function () {
         this.el.addEventListener('click', function (evt) {
			document.getElementById('text').setAttribute('value',"Bravo tu as bien pris le donut !");
			var element = document.getElementById('donutremove');
			element.parentNode.removeChild(element);
			setTimeout(postNbDonut,5000);
      });
    }
	});
	AFRAME.registerComponent('animation-marge', {
    init: function () {
        this.el.addEventListener('animationcomplete', function(e) {
			if(document.getElementById('donutremove') != null){
				document.getElementById('text').setAttribute('value',"Trop tard tu as perdu !");
				var element = document.getElementById('donutremove');
				element.parentNode.removeChild(element);
				setTimeout(redir,5000);
			}
		});
		this.el.addEventListener('click', function(e) {
			document.getElementById('margeEntity').setAttribute('rotation','90 0 0');
			document.getElementById('text').setAttribute('value',"Marge est morte ! Prend le donut !");
			
		});
    }
	});
	function redir(){
		document.location.href="index.php";
	}
	function postNbDonut(){
		//Création dynamique du formulaire
			var form = document.createElement('form');
			form.setAttribute('method', "POST");
			form.setAttribute('action', "scenevr.php");
			var champCache = document.createElement('input');
			champCache.setAttribute('type', 'hidden');
			champCache.setAttribute('name', "nbDonut");
			champCache.setAttribute('value', nbDonut+1);
			form.appendChild(champCache);
			
			//Ajout du formulaire à la page et soumission du formulaire
			document.body.appendChild(form);
			form.submit();
	}
	</script>
    <a-scene>
        <a-assets>
			<a-asset-item id="donut" src="donut.glb"></a-asset-item>
			<a-asset-item id="marge" src="marge.glb"></a-asset-item>
			<a-asset-item id="maison" src="maison.glb"></a-asset-item>
			<a-asset-item response-type="arraybuffer" id="tir" src="tir.mp3"></a-asset-item>
		</a-assets> 
		
		<a-text id="text" value="Prend le donut avant Marge !" color="#000"
        position="-2 3.5 -3" scale="1.5 1.5 1.5"></a-text>
		<a-light type="ambient" color="#445451"></a-light>
		
		<a-entity id="donutremove" cursor-listener gltf-model="#donut" position="0 1 -3" scale="3 3 3" rotation="90 0 0"></a-entity>
		<a-entity id="margeEntity" animation-marge gltf-model="#marge" position="2 0 -6" scale="0.01 0.01 0.01" rotation="0 -35 0" 
		animation="property: position; to: 0.8 0 -3; dur: 10000; delay:5000; pauseEvents: click; easing: linear"
		sound="src: #tir; on: click">
		</a-entity>
		<a-entity gltf-model="#maison" position="0 -0.2 -15" scale="0.01 0.01 0.01" ></a-entity>
        <a-entity environment="preset: forest"></a-entity>
		
		<a-camera>
			<a-cursor></a-cursor>
		</a-camera>
    </a-scene>
	
   </body>
</html>
