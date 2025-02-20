extends CharacterBody2D

@export var speed : float = 300.0

func _physics_process(_delta):
  # Get input for all directions
  var input_dir = Input.get_vector("move_left", "move_right", "move_up", "move_down")
  velocity = input_dir * speed
  
  # Move and collide with obstacles
  move_and_slide()

func _ready():
  # Block arrow keys and spacebar from scrolling the page
  OS.execute("eval", [
	'document.addEventListener("keydown", function(e) { if([32, 37, 38, 39, 40].indexOf(e.keyCode) > -1) { e.preventDefault(); }});'
  ])
