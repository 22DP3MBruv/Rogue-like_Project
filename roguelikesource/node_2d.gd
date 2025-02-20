extends Node2D


# Called when the node enters the scene tree for the first time.
func _ready():
	# Block browser default keyboard actions (arrow keys + spacebar)
	OS.execute("eval", [
	'document.addEventListener("keydown", function(e) { if([32, 37, 38, 39, 40].indexOf(e.keyCode) > -1) { e.preventDefault(); }});'
	])


# Called every frame. 'delta' is the elapsed time since the previous frame.
func _process(delta: float) -> void:
	pass
