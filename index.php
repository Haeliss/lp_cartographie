<?php 
session_start();
/*session is started if you don't write this line can't use $_Session  global variable*/
if(isset($_POST['useGeoloc'])) {
	$_SESSION["useGeoloc"] = $_POST['useGeoloc'];
	$_POST['useGeoloc'] = null;
}
if(!isset($_SESSION["nbDonut"])){
$_SESSION["nbDonut"]=0;}
if(!isset($_SESSION["useGeoloc"])){
$_SESSION["useGeoloc"]= true;}
$nbDonut = $_SESSION["nbDonut"];
$useGeoloc = $_SESSION["useGeoloc"];
?>
<!DOCTYPE html>
<html>
<head>
<meta charset='utf-8' />
<title>Find the donuts !</title>
<meta name='viewport' content='initial-scale=1,maximum-scale=1,user-scalable=no' />
<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.js'></script>
<script src='https://unpkg.com/three@0.102.0/build/three.min.js'></script>
<script src="https://unpkg.com/three@0.102.0/examples/js/loaders/GLTFLoader.js"></script>
<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.53.1/mapbox-gl.css' rel='stylesheet' />
<link href='style.css' rel='stylesheet' />
</head>
<body>
<div id='map'></div>
<p id="nbDonut">Nombre de donuts : 0</p>
<div id="switch">
<input type="checkbox" id="static" name="static">
<label for="static">Mode static</label>	</div>
<p id="about">i</p>
<script>
mapboxgl.accessToken = 'pk.eyJ1IjoiaGFlbGlzcyIsImEiOiJjanNsaWFqdWkxcngyNDNwNmkyZHRhZmJmIn0.Ge7r-3h2afU9x_IQwKDrBw';
var lon,lat,position;
var useGeoloc = <?php echo $useGeoloc; ?>;
if(useGeoloc == true){
	document.getElementById('static').checked = false;
} else document.getElementById('static').checked = true;
var center = [7.25000, 43.70000];
var donutPosition = [
[2.63704,48.84991],
[7.17180,43.65664],
[7.18092,43.65490],
[7.23093,43.67862],
[7.33508,43.69107],
[7.33217,43.70322],
[7.33715,43.70723],
[7.35111,43.71671],
[7.41592,43.72374]
];
//Récupération de la variable dans le tableau
var nbDonut = <?php echo $nbDonut; ?>;
document.getElementById('nbDonut').innerHTML = "Nombre de donuts : " + nbDonut;
if(!useGeoloc){
	if(nbDonut < donutPosition.length){
		center = donutPosition[nbDonut];
	}
}
var map = new mapboxgl.Map({
container: 'map', // container id
style: 'mapbox://styles/mapbox/streets-v11', // stylesheet location
center: center, // starting position [lng, lat]
zoom: 18 // starting zoom
});

if(nbDonut >= donutPosition.length) {
  // create a HTML element for each feature
  var el = document.createElement('div');
  el.className = 'marker';

  new mapboxgl.Marker(el)
  .setLngLat(center)
  .setPopup(new mapboxgl.Popup({ offset: 25 }) // add popups
    .setHTML('<h3>Bravo tu as trouvé tout les donuts !</h3>'))
  .addTo(map);
}

var geolocate = new mapboxgl.GeolocateControl({
    positionOptions: {
        enableHighAccuracy: true
    },
    trackUserLocation: true
})

map.addControl(geolocate);

geolocate.on('geolocate', function(e) {
      lon = e.coords.longitude;
      lat = e.coords.latitude
      position = [lon, lat];
	  console.log(position);
	  if(lon.toFixed(3) == donutPosition[nbDonut][0].toFixed(3) && lat.toFixed(3) == donutPosition[nbDonut][1].toFixed(3)){
		var mapLayer = map.getLayer('3d-model');
		if((typeof mapLayer === 'undefined') && (nbDonut < donutPosition.length) && useGeoloc){	
			map.addLayer(customLayer, 'waterway-label');
		}
	  }
});
var loader = new THREE.GLTFLoader();
// parameters to ensure the model is georeferenced correctly on the map
if(nbDonut < donutPosition.length){
	var modelOrigin = donutPosition[nbDonut];
} else {
	var modelOrigin = center;
}
var modelAltitude = 0;
var modelRotate = [Math.PI, 0, 0];
var modelScale = 5.41843220338983e-8;
 
// transformation parameters to position, rotate and scale the 3D model onto the map
var modelTransform = {
translateX: mapboxgl.MercatorCoordinate.fromLngLat(modelOrigin, modelAltitude).x,
translateY: mapboxgl.MercatorCoordinate.fromLngLat(modelOrigin, modelAltitude).y,
translateZ: mapboxgl.MercatorCoordinate.fromLngLat(modelOrigin, modelAltitude).z,
rotateX: modelRotate[0],
rotateY: modelRotate[1],
rotateZ: modelRotate[2],
scale: modelScale
};
 
var THREE = window.THREE;
 
// configuration of the custom layer for a 3D model per the CustomLayerInterface
var customLayer = {
id: '3d-model',
type: 'custom',
renderingMode: '3d',
onAdd: function(map, gl) {
this.camera = new THREE.Camera();
this.scene = new THREE.Scene();
 
// create two three.js lights to illuminate the model
var directionalLight = new THREE.DirectionalLight(0xffffff);
directionalLight.position.set(0, -70, -30).normalize();
this.scene.add(directionalLight);
 
var directionalLight2 = new THREE.DirectionalLight(0xffffff);
directionalLight2.position.set(0, 70, -30).normalize();
this.scene.add(directionalLight2);
 
// use the three.js GLTF loader to add the 3D model to the three.js scene

loader.load('donutXXL.glb', (function (glb) {
this.scene.add(glb.scene);
}).bind(this));
this.map = map;
 
// use the Mapbox GL JS map canvas for three.js
this.renderer = new THREE.WebGLRenderer({
canvas: map.getCanvas(),
context: gl
});
 
this.renderer.autoClear = false;
},
render: function(gl, matrix) {
var rotationX = new THREE.Matrix4().makeRotationAxis(new THREE.Vector3(1, 0, 0), modelTransform.rotateX);
var rotationY = new THREE.Matrix4().makeRotationAxis(new THREE.Vector3(0, 1, 0), modelTransform.rotateY);
var rotationZ = new THREE.Matrix4().makeRotationAxis(new THREE.Vector3(0, 0, 1), modelTransform.rotateZ);
 
var m = new THREE.Matrix4().fromArray(matrix);
var l = new THREE.Matrix4().makeTranslation(modelTransform.translateX, modelTransform.translateY, modelTransform.translateZ)
.scale(new THREE.Vector3(modelTransform.scale, -modelTransform.scale, modelTransform.scale))
.multiply(rotationX)
.multiply(rotationY)
.multiply(rotationZ);
 
this.camera.projectionMatrix.elements = matrix;
this.camera.projectionMatrix = m.multiply(l);
this.renderer.state.reset();
this.renderer.render(this.scene, this.camera);
this.map.triggerRepaint();
}
}

map.on('touch', function(e){
    var click_lng = e.lngLat.lng.toFixed(4);
	var click_lat = e.lngLat.lat.toFixed(4);
	var bool = recherche(click_lng,click_lat);
	if(bool == true) {
		map.removeLayer("3d-model");
		document.location.href="scenevr.php";
	}
});
map.on('click', function(e){
	var click_lng = e.lngLat.lng.toFixed(4);
	var click_lat = e.lngLat.lat.toFixed(4);
	var bool = recherche(click_lng,click_lat);
	if(bool == true) {
		map.removeLayer("3d-model");
		document.location.href="scenevr.php";
	}
});
function recherche(lon, lat){
	var reponse = false;
	var element = donutPosition[nbDonut];
	var elem_lon = element[0];
	var elem_lat = element[1];
	if(elem_lon.toFixed(4)== lon && elem_lat.toFixed(4) == lat){
		reponse= true;
	}
	return reponse;
}
map.on('style.load', function() {
	if((nbDonut < donutPosition.length) && !useGeoloc){
		map.addLayer(customLayer, 'waterway-label');
	}
});

document.getElementById('about').addEventListener('click',function(e){
	document.location.href="about.php";
});
document.getElementById('static').addEventListener('click',function(e){
	if(document.getElementById('static').checked == true){
		useGeoloc = false;
	} else {
		useGeoloc = true;
	}
	//Création dynamique du formulaire
			var form = document.createElement('form');
			form.setAttribute('method', "POST");
			form.setAttribute('action', "index.php");
			var champCache = document.createElement('input');
			champCache.setAttribute('type', 'hidden');
			champCache.setAttribute('name', "useGeoloc");
			champCache.setAttribute('value', useGeoloc);
			form.appendChild(champCache);
			
			//Ajout du formulaire à la page et soumission du formulaire
			document.body.appendChild(form);
			form.submit();
});
</script>
 
</body>
</html>