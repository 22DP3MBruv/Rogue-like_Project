extends ParallaxBackground

@export var scroll_speed := 50.0
@export var camera_path: NodePath # Set this in the inspector

var camera: Camera2D

func _ready():
	# Get reference to camera when scene loads
	camera = get_node(camera_path)

func _process(delta):
	if camera:
		scroll_offset = -camera.get_screen_center() * 0.5
