extends Node2D

@export var speed = 300  # Pixels per second
var player: Sprite2D

func _ready():
	player = $Player  # Gets the Player node
	# Automatically resize viewport to window
	get_tree().root.content_scale_mode = Window.CONTENT_SCALE_MODE_CANVAS_ITEMS
	get_tree().root.content_scale_aspect = Window.CONTENT_SCALE_ASPECT_EXPAND

func _process(delta):
	var direction = Input.get_vector("move_left", "move_right", "move_up", "move_down")
	player.position += direction * speed * delta

func _input(event):
	if event.is_action_pressed("ui_cancel"):  # ESC key
		var pause_menu = get_node("PauseMenu")
		pause_menu.visible = not pause_menu.visible
		get_tree().paused = pause_menu.visible
