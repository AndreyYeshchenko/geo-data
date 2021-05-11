var items = []; var lat = 46.4631383; var lng = 30.6971888; // set default lat/lang for case if no user's coordinates from browser
window.onload = function() {

    var txt = document.getElementById('txt');
    var list = document.getElementById('list');
    var ruleset = document.getElementById('ruleset');
	var tags = document.getElementById('tagsId').value;
	items = tags.split(','); // split input string, divided by comma
	items = items.filter(function(e){return e});	// checking of empty items
	
    if ((typeof txt !== 'undefined') && (typeof items !== 'undefined')) {
        txt.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                let val = txt.value;
                if (val !== '') {
                    if (items.indexOf(val) >= 0) {
                        alert('Tag name is a duplicate');
                    } else {
                        items.push(val);
                        render();
                        txt.value = '';
                        txt.focus();
                    }
                } else {
                    alert('Please type a tag Name');
                }
            }
        });
    }

    render();
    txt.focus();
	
	google.maps.event.addDomListener(window, 'load', initMap);
}


function render() {
    if (typeof items !== 'undefined') {
        list.innerHTML = ''; var tags_list = '';		
        items.map((item, index) => {
            list.innerHTML += `<li><span>${item}</span><a href="javascript: remove(${index})">×</a></li>`;			
			tags_list += item;		
			if ( index < ( items.length - 1 ) )	tags_list +=  ',';
       });	   
	  
	document.getElementById('tagsId').value = tags_list;
    }
}

function remove(i) {
    items = items.filter(item => items.indexOf(item) != i);
    render();
}

// JS code for Geofencing component
var drawingManager;
	var selectedShape;
	var colors = ['#1E90FF', '#FF1493', '#32CD32', '#FF8C00', '#4B0082'];
	var selectedColor;
	var colorButtons = {};

function clearSelection () {
	if (selectedShape) {
		if (selectedShape.type !== 'marker') {
			selectedShape.setEditable(false);
		}
		
		selectedShape = null;
	}
}

function setSelection (shape) {
	console.log(shape);
	if ( shape.type !== 'marker' ) {
		clearSelection();
		shape.setEditable(true);
		selectColor(shape.get('fillColor') || shape.get('strokeColor'));
	}
	if ( shape.type == 'circle' ) {
		ruleset.innerHTML += `<li style="width: 120px; background: #ff8d80; padding:0;">
		<div style="background: #2e96c5; color: white; fontWeight: bold; text-align: center; margin: 5px;">Geofence Circle</div>
		<div style="background: #ff8d80;">
		<p style="background: white; margin: 5px;">Lat=${shape.center.lat().toFixed(7)}</p>
		<p style="background: white; margin: 5px;">Lng=${shape.center.lng().toFixed(7)}</p>
		<p style="background: white; margin: 5px;">R=${shape.radius.toFixed(7)}</p></span>	
		</div></li>`;
		//alert('lng='+shape.center.lng()+ ' lat='+shape.center.lat()+' radius='+shape.radius);
	}

	if ( shape.type == 'rectangle' ) {
		ruleset.innerHTML += `<li style="width: 120px; background: #ff8d80; padding:0; vertical-align: top">
		<div style="background: #2e96c5; color: white; fontWeight: bold; text-align: center; margin: 5px;">Geofence Rectangle</div>
		<div style="background: #ff8d80;">
		<p style="background: white; margin: 5px;">Lat1=${shape.bounds.Ua.g.toFixed(7)}</p>
		<p style="background: white; margin: 5px;">Lng1=${shape.bounds.La.g.toFixed(7)}</p>
		<p style="background: white; margin: 5px;">Lat1=${shape.bounds.Ua.i.toFixed(7)}</p>
		<p style="background: white; margin: 5px;">Lng2=${shape.bounds.La.i.toFixed(7)}</p>
		</div>`;
		//alert('lng='+shape.bounds.La.g+ ' lat='+shape.bounds.Ua.g+' radius='+shape.radius);
	}
	selectedShape = shape;
	
}

function deleteSelectedShape () {
	if (selectedShape) {
		selectedShape.setMap(null);
	}
}

function selectColor (color) {
	selectedColor = color;
	for (var i = 0; i < colors.length; ++i) {
		var currColor = colors[i];
		colorButtons[currColor].style.border = currColor == color ? '2px solid #789' : '2px solid #fff';
	}

	// Retrieves the current options from the drawing manager and replaces the
	// stroke or fill color as appropriate.
	

	var rectangleOptions = drawingManager.get('rectangleOptions');
	rectangleOptions.fillColor = color;
	drawingManager.set('rectangleOptions', rectangleOptions);

	var circleOptions = drawingManager.get('circleOptions');
	circleOptions.fillColor = color;
	drawingManager.set('circleOptions', circleOptions);

	
}

function setSelectedShapeColor (color) {
	if (selectedShape) {
		if (selectedShape.type == google.maps.drawing.OverlayType.POLYLINE) {
			selectedShape.set('strokeColor', color);
		} else {
			selectedShape.set('fillColor', color);
		}
	}
}

function makeColorButton (color) {
	var button = document.createElement('span');
	button.className = 'color-button';
	button.style.backgroundColor = color;
	google.maps.event.addDomListener(button, 'click', function () {
		selectColor(color);
		setSelectedShapeColor(color);
	});

	return button;
}

function buildColorPalette () {
	var colorPalette = document.getElementById('color-palette');
	for (var i = 0; i < colors.length; ++i) {
		var currColor = colors[i];
		var colorButton = makeColorButton(currColor);
		colorPalette.appendChild(colorButton);
		colorButtons[currColor] = colorButton;
	}
	selectColor(colors[0]);
}
function getLocation() {  // works only on https!!!!
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(setPosition);	
  } 
}

function setPosition(position) {
  lat = position.coords.latitude; lng = position.coords.longitude;
}

function initMap () {
	getLocation();
	var map = new google.maps.Map(document.getElementById('map'), {
		zoom: 16,
		center: new google.maps.LatLng( lat, lng ),
		mapTypeId: google.maps.MapTypeId.SATELLITE,
		mapTypeControl: true,
		disableDefaultUI: true,  
		zoomControl: true,            
		streetViewControl: true,
	});

	var polyOptions = {
		strokeWeight: 0,
		fillOpacity: 0.45,
		editable: true,
		draggable: true
	};
	// Creates a drawing manager attached to the map that allows the user to draw
	// markers, lines, and shapes.

	drawingManager = new google.maps.drawing.DrawingManager({
		drawingMode: google.maps.drawing.OverlayType.CIRCLE,
		drawingControl: true,
		drawingControlOptions: {
		position: google.maps.ControlPosition.TOP_LEFT,
		drawingModes: [
				//google.maps.drawing.OverlayType.MARKER,
				google.maps.drawing.OverlayType.CIRCLE,                            
				google.maps.drawing.OverlayType.RECTANGLE,
]
		},
		markerOptions: {
			draggable: true
		},
		polylineOptions: {
			editable: true,
			draggable: true
		},
		rectangleOptions: polyOptions,
		circleOptions: polyOptions,
		polygonOptions: polyOptions,
		map: map
	});

	google.maps.event.addListener(drawingManager, 'overlaycomplete', function (e) {
		var newShape = e.overlay;
		
		newShape.type = e.type;
		//console.log(newShape.type);
		if (e.type !== google.maps.drawing.OverlayType.MARKER) {
			// Switch back to non-drawing mode after drawing a shape.
			drawingManager.setDrawingMode(null);

			// Add an event listener that selects the newly-drawn shape when the user
			// mouses down on it.
			google.maps.event.addListener(newShape, 'click', function (e) {
				
				setSelection(newShape);
			});
			setSelection(newShape);
		}
		else {
			google.maps.event.addListener(newShape, 'click', function (e) {
				setSelection(newShape);
			});
			setSelection(newShape);
		}
	});

	// Clear the current selection when the drawing mode is changed, or when the
	// map is clicked.
	google.maps.event.addListener(drawingManager, 'drawingmode_changed', clearSelection);
	google.maps.event.addListener(map, 'click', clearSelection);
	google.maps.event.addDomListener(document.getElementById('delete-button'), 'click', deleteSelectedShape);

	buildColorPalette();
}

// Section for drag-and-drop Rule Editor
function dragStart(ev) {
    ev.dataTransfer.effectAllowed='move';
    ev.dataTransfer.setData("Text", ev.target.getAttribute('id'));   
    ev.dataTransfer.setDragImage(ev.target,100,100);
    return true;
 }
function dragEnter(ev) {
	ev.preventDefault();
	return true;
}
function dragOver(ev) {
    ev.preventDefault();
}
function dragDrop(ev) {
	var data = ev.dataTransfer.getData("Text");
	ev.target.appendChild(document.getElementById(data));
	ev.stopPropagation();
	return false;
}
