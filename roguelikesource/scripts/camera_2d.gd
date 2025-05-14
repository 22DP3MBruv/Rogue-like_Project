extends Camera2D

@export var follow_speed = 5.0  # Higher = faster follow
var target_position = Vector2.ZERO

func _process(delta):
	# Get player's global position
	target_position = get_parent().global_position
	
	# Smoothly interpolate camera position
	global_position = global_position.lerp(target_position, follow_speed * delta)
