<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <script src="{{ asset('js/webcam.js') }}"></script>
</head>
<body>

        <canvas id="webcam"> </canvas>
            
    <script>
    camera.init({
	width: 160, // default: 640
	height: 120, // default: 480
	fps: 30, // default: 30
	mirror: true,  // default: false
	targetCanvas: document.getElementById('webcam'), // default: null 

	onFrame: function(canvas) {
		// do something with image data found in the canvas argument
	},

	onSuccess: function() {
		// stream succesfully started, yay!
	},

	onError: function(error) {
		// something went wrong on initialization
	},

	onNotSupported: function() {
		// instruct the user to get a better browser
	}
});
    </script>
</body>
</html>